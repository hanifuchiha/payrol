<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model {

	public function get_all()
	{
		return $this->db->select("CONCAT(LPAD(payroll_month, 2, '0'), '/', payroll_year) AS period, COUNT(*) AS total_employee, SUM(net_salary) AS total_net, status", FALSE)
			->from('payrolls')
			->group_by(array('payroll_year', 'payroll_month', 'status'))
			->order_by('payroll_year DESC, payroll_month DESC, status ASC')
			->get()
			->result_array();
	}

	public function get_slips()
	{
		return $this->db->select('payrolls.id, payrolls.payroll_month, payrolls.payroll_year, payrolls.base_salary, payrolls.bonus, payrolls.deduction, payrolls.kasbon, payrolls.net_salary, payrolls.status, employees.name AS employee_name, employees.nik')
			->from('payrolls')
			->join('employees', 'employees.id = payrolls.employee_id')
			->order_by('payrolls.payroll_year DESC, payrolls.payroll_month DESC, employees.name ASC')
			->get()
			->result_array();
	}
}
