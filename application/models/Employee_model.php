<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->ensure_columns();
	}

	public function get_all()
	{
		$rows = $this->db->select('employees.*, departments.division_name, departments.position_name')
			->from('employees')
			->join('departments', 'departments.id = employees.department_id', 'left')
			->order_by('employees.name', 'ASC')
			->get()
			->result_array();

		foreach ($rows as &$row) {
			$row['contract_remaining_text'] = $this->build_contract_remaining_text($row);
		}
		unset($row);

		return $rows;
	}

	public function find($id)
	{
		$row = $this->db->get_where('employees', array('id' => $id))->row_array();
		if ($row) {
			$row['contract_remaining_text'] = $this->build_contract_remaining_text($row);
		}
		return $row;
	}

	public function insert($data)
	{
		return $this->db->insert('employees', $data);
	}

	public function update($id, $data)
	{
		return $this->db->where('id', $id)->update('employees', $data);
	}

	public function delete($id)
	{
		return $this->db->delete('employees', array('id' => $id));
	}

	public function find_by_nik($nik)
	{
		return $this->db->get_where('employees', array('nik' => $nik))->row_array();
	}

	public function normalize_phone($value)
	{
		$digits = preg_replace('/\D+/', '', (string) $value);
		if ($digits === '') {
			return '';
		}

		if (strpos($digits, '62') === 0) {
			return $digits;
		}
		if ($digits[0] === '0') {
			return '62' . substr($digits, 1);
		}
		if ($digits[0] === '8') {
			return '62' . $digits;
		}

		return $digits;
	}

	private function ensure_columns()
	{
		$this->ensure_column('npwp', "ALTER TABLE `employees` ADD COLUMN `npwp` varchar(50) DEFAULT NULL AFTER `phone`");
		$this->ensure_column('bpjs', "ALTER TABLE `employees` ADD COLUMN `bpjs` varchar(50) DEFAULT NULL AFTER `npwp`");
		$this->ensure_column('join_date', "ALTER TABLE `employees` ADD COLUMN `join_date` date DEFAULT NULL AFTER `employment_status`");
		$this->ensure_column('contract_start_date', "ALTER TABLE `employees` ADD COLUMN `contract_start_date` date DEFAULT NULL AFTER `join_date`");
		$this->ensure_column('contract_end_date', "ALTER TABLE `employees` ADD COLUMN `contract_end_date` date DEFAULT NULL AFTER `contract_start_date`");
	}

	private function ensure_column($column, $sql)
	{
		if (! $this->db->field_exists($column, 'employees')) {
			$this->db->query($sql);
		}
	}

	private function build_contract_remaining_text($employee)
	{
		$status = trim((string) ($employee['employment_status'] ?? ''));
		$start = trim((string) ($employee['contract_start_date'] ?? ''));
		$end = trim((string) ($employee['contract_end_date'] ?? ''));
		if (strcasecmp($status, 'Kontrak') !== 0 || $start === '' || $end === '') {
			return '-';
		}

		try {
			$today = new DateTime('today');
			$startDate = new DateTime($start);
			$endDate = new DateTime($end);
		} catch (Exception $e) {
			return '-';
		}

		if ($endDate < $today) {
			return 'Habis';
		}

		if ($startDate > $endDate) {
			return '-';
		}

		$interval = $today->diff($endDate);
		$days = (int) $interval->days;
		if ($days === 0) {
			return 'Hari ini';
		}

		$months = ($interval->y * 12) + $interval->m;
		$parts = array();
		if ($months > 0) {
			$parts[] = $months . ' bulan';
		}
		if ($interval->d > 0) {
			$parts[] = $interval->d . ' hari';
		}
		if (empty($parts)) {
			$parts[] = $days . ' hari';
		}

		return implode(' ', $parts) . ' lagi';
	}
}
