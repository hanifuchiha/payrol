<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_model extends CI_Model {

	public function get_all($month = NULL, $year = NULL)
	{
		if ($month !== NULL) $this->db->where('payrolls.payroll_month', (int) $month);
		if ($year !== NULL) $this->db->where('payrolls.payroll_year', (int) $year);
		return $this->db->select('payrolls.*, employees.name AS employee_name, employees.nik, COALESCE(k.total_kasbon, 0) - COALESCE(p.total_paid, 0) AS sisa_kasbon', FALSE)
			->from('payrolls')
			->join('employees', 'employees.id = payrolls.employee_id')
			->join('(SELECT employee_id, SUM(amount) AS total_kasbon FROM kasbons GROUP BY employee_id) k', 'k.employee_id = payrolls.employee_id', 'left')
			->join('(SELECT employee_id, SUM(kasbon) AS total_paid FROM payrolls WHERE status = "Disetujui" GROUP BY employee_id) p', 'p.employee_id = payrolls.employee_id', 'left')
			->order_by('payroll_year DESC, payroll_month DESC, employees.name ASC')
			->get()
			->result_array();
	}

	public function get_summary($month = NULL, $year = NULL)
	{
		if ($month !== NULL) $this->db->where('payroll_month', (int) $month);
		if ($year !== NULL) $this->db->where('payroll_year', (int) $year);
		$row = $this->db->select('COUNT(*) AS total_employee, COALESCE(SUM(gross_salary),0) AS total_gross, COALESCE(SUM(total_deduction),0) AS total_deduction, COALESCE(SUM(net_salary),0) AS total_net, COALESCE(SUM(kasbon),0) AS total_kasbon')
			->get('payrolls')
			->row_array();

		$row['period'] = sprintf('%02d/%04d', $month ?: date('n'), $year ?: date('Y'));

		return $row;
	}

	public function find($id)
	{
		return $this->db->get_where('payrolls', array('id' => $id))->row_array();
	}

	public function get_slip($id)
	{
		return $this->db->select('payrolls.*, employees.name AS employee_name, employees.nik, employees.phone, employees.employment_status, departments.division_name, departments.position_name, COALESCE(k.total_kasbon, 0) - COALESCE(p.total_paid, 0) AS sisa_kasbon', FALSE)
			->from('payrolls')
			->join('employees', 'employees.id = payrolls.employee_id')
			->join('departments', 'departments.id = employees.department_id', 'left')
			->join('(SELECT employee_id, SUM(amount) AS total_kasbon FROM kasbons GROUP BY employee_id) k', 'k.employee_id = payrolls.employee_id', 'left')
			->join('(SELECT employee_id, SUM(kasbon) AS total_paid FROM payrolls WHERE status = "Disetujui" GROUP BY employee_id) p', 'p.employee_id = payrolls.employee_id', 'left')
			->where('payrolls.id', $id)
			->get()
			->row_array();
	}

	public function insert($data)
	{
		return $this->save_with_finance(NULL, $data);
	}

	public function update($id, $data)
	{
		return $this->save_with_finance($id, $data);
	}

	private function save_with_finance($id, $data)
	{
		$debug = $this->db->db_debug;
		$this->db->db_debug = FALSE;
		$this->db->trans_begin();
		// Lock the saved row before testing approval, including concurrent requests.
		if ($id !== NULL) {
			$current = $this->db->query('SELECT id, status FROM payrolls WHERE id = ? FOR UPDATE', array((int) $id));
			$current = $current ? $current->row_array() : NULL;
			if (!$current || $current['status'] === 'Disetujui') {
				$this->db->trans_rollback();
				$this->db->db_debug = $debug;
				return FALSE;
			}
		}
		// Serialize approvals for an employee so two periods cannot repay the same debt.
		$employee = $this->db->query('SELECT id FROM employees WHERE id = ? FOR UPDATE', array((int) $data['employee_id']));
		if (!$employee || !$employee->row_array()) {
			$this->db->trans_rollback();
			$this->db->db_debug = $debug;
			return FALSE;
		}
		$this->load->model('Kasbon_model');
		if ((float) $data['kasbon'] < 0 || (float) $data['kasbon'] > $this->Kasbon_model->get_remaining_by_employee((int) $data['employee_id'], $id)) {
			$this->db->trans_rollback();
			$this->db->db_debug = $debug;
			return FALSE;
		}

		$ok = $id === NULL ? $this->db->insert('payrolls', $data) : $this->db->where('id', $id)->update('payrolls', $data);
		if ($id === NULL && $ok) $id = $this->db->insert_id();
		if ($ok) $ok = $this->db->delete('payroll_finance_claims', array('payroll_id'=>(int) $id));
		foreach (json_decode($data['finance_details'] ?? '[]', TRUE) ?: array() as $item) {
			if (!$ok) break;
			$ok = $this->db->insert('payroll_finance_claims', array('source_key'=>$item['key'], 'payroll_id'=>(int) $id));
		}
		if ($ok && $this->db->trans_status()) $this->db->trans_commit();
		else { $this->db->trans_rollback(); $ok = FALSE; }
		$this->db->db_debug = $debug;
		return $ok;
	}

	public function delete($id)
	{
		// Conditional delete is atomic if approval races with this request.
		$ok = $this->db->where('status !=', 'Disetujui')->delete('payrolls', array('id' => (int) $id));
		return $ok && $this->db->affected_rows() === 1;
	}

	public function exists_for_period($employee_id, $month, $year, $exclude_id = NULL)
	{
		$this->db->from('payrolls')
			->where('employee_id', $employee_id)
			->where('payroll_month', $month)
			->where('payroll_year', $year);

		if ($exclude_id !== NULL)
		{
			$this->db->where('id !=', $exclude_id);
		}

		return $this->db->count_all_results() > 0;
	}
}
