<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kasbon_model extends CI_Model {

	public function get_all()
	{
		return $this->db->select('kasbons.*, employees.name AS employee_name, employees.nik')
			->from('kasbons')
			->join('employees', 'employees.id = kasbons.employee_id')
			->order_by('transaction_date DESC, kasbons.id DESC')
			->get()
			->result_array();
	}

	public function find($id)
	{
		return $this->db->get_where('kasbons', array('id' => $id))->row_array();
	}

	public function insert($data)
	{
		return $this->db->insert('kasbons', $data);
	}

	public function update($id, $data)
	{
		return $this->db->where('id', $id)->update('kasbons', $data);
	}

	public function delete($id)
	{
		return $this->db->delete('kasbons', array('id' => $id));
	}

	public function get_total_by_employee_period($employee_id, $month, $year)
	{
		$row = $this->db->select('COALESCE(SUM(amount), 0) AS total_amount', FALSE)
			->from('kasbons')
			->where('employee_id', $employee_id)
			->where('MONTH(transaction_date)', (int) $month)
			->where('YEAR(transaction_date)', (int) $year)
			->get()
			->row_array();

		return (float) $row['total_amount'];
	}

	public function get_total_by_employee($employee_id)
	{
		$row = $this->db->select('COALESCE(SUM(amount), 0) AS total_amount', FALSE)
			->from('kasbons')
			->where('employee_id', (int) $employee_id)
			->get()
			->row_array();

		return (float) ($row['total_amount'] ?? 0);
	}

	public function get_paid_by_employee($employee_id, $exclude_payroll_id = NULL)
	{
		$this->db->select('COALESCE(SUM(kasbon), 0) AS total_paid', FALSE)
			->from('payrolls')
			->where('status', 'Disetujui')
			->where('employee_id', (int) $employee_id);

		if ($exclude_payroll_id !== NULL) {
			$this->db->where('id !=', (int) $exclude_payroll_id);
		}

		$row = $this->db->get()->row_array();
		return (float) ($row['total_paid'] ?? 0);
	}

	public function get_remaining_by_employee($employee_id, $exclude_payroll_id = NULL)
	{
		$total = $this->get_total_by_employee($employee_id);
		$paid = $this->get_paid_by_employee($employee_id, $exclude_payroll_id);
		$remaining = $total - $paid;
		return $remaining > 0 ? $remaining : 0;
	}

	public function get_summary_by_employee()
	{
		return $this->db->query("
			SELECT
				e.id AS employee_id,
				e.nik,
				e.name AS employee_name,
				COALESCE(k.total_kasbon, 0) AS total_kasbon,
				COALESCE(p.total_potong_kasbon, 0) AS total_potong_kasbon,
				COALESCE(k.total_kasbon, 0) - COALESCE(p.total_potong_kasbon, 0) AS sisa_kasbon,
				CASE
					WHEN COALESCE(k.total_kasbon, 0) - COALESCE(p.total_potong_kasbon, 0) <= 0 THEN 'Lunas'
					ELSE 'Belum Lunas'
				END AS status_pelunasan
			FROM employees e
			LEFT JOIN (
				SELECT employee_id, SUM(amount) AS total_kasbon
				FROM kasbons
				GROUP BY employee_id
			) k ON k.employee_id = e.id
			LEFT JOIN (
				SELECT employee_id, SUM(kasbon) AS total_potong_kasbon
				FROM payrolls
				WHERE status = 'Disetujui'
				GROUP BY employee_id
			) p ON p.employee_id = e.id
			ORDER BY e.name ASC
		")->result_array();
	}
}
