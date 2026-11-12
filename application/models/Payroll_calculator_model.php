<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_calculator_model extends CI_Model {
	public function calculate($employee_id, $month, $year, $bonus = 0, $deduction = 0, $kasbon = 0, $status = 'Draft')
	{
		$employee = $this->db->get_where('employees', array('id'=>(int) $employee_id))->row_array();
		if (!$employee) throw new RuntimeException('Data karyawan tidak ditemukan.');
		$this->load->model('Prayer_bonus_model');
		$this->load->model('Attendance_pay_model');
		$this->load->model('Finance_pay_model');
		$prayer = $this->Prayer_bonus_model->for_employee($employee_id, $month, $year);
		$attendance = $this->Attendance_pay_model->for_employee($employee_id, $month, $year);
		$finance = $this->Finance_pay_model->for_employee($attendance['source_id'], $attendance['period']);
		$gross = (float) $employee['base_salary'] + $bonus + $prayer['amount'] + $attendance['overtime_pay'] + $attendance['daily_pay'] + $finance['addition'];
		$weekly_daily_paid = $attendance['daily_pay'];
		$total_deduction = $deduction + $kasbon + $finance['deduction'] + $weekly_daily_paid;
		return array('employee_id'=>(int) $employee_id, 'payroll_month'=>(int) $month, 'payroll_year'=>(int) $year,
			'base_salary'=>(float) $employee['base_salary'], 'bonus'=>$bonus,
			'prayer_bonus'=>$prayer['amount'], 'prayer_count'=>$prayer['count'], 'prayer_rank'=>$prayer['rank'], 'prayer_period'=>$prayer['period'],
			'overtime_pay'=>$attendance['overtime_pay'], 'overtime_minutes'=>$attendance['overtime_minutes'], 'overtime_days'=>$attendance['overtime_days'],
			'attendance_days'=>$attendance['attendance_days'], 'daily_rate'=>$attendance['daily_rate'], 'daily_pay'=>$attendance['daily_pay'], 'weekly_daily_paid'=>$weekly_daily_paid,
			'finance_addition'=>$finance['addition'], 'finance_deduction'=>$finance['deduction'], 'finance_details'=>json_encode($finance['items'], JSON_UNESCAPED_UNICODE),
			'deduction'=>$deduction, 'kasbon'=>$kasbon, 'gross_salary'=>$gross, 'total_deduction'=>$total_deduction, 'net_salary'=>$gross-$total_deduction, 'status'=>$status);
	}
}
