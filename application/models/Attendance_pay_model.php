<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance_pay_model extends CI_Model {
	public function for_employee($employee_id, $month, $year)
	{
		$month = (int) $month;
		$year = (int) $year;
		if ($month < 1 || $month > 12 || $year < 1900 || $year > 9998) {
			throw new InvalidArgumentException('Periode payroll tidak valid.');
		}
		$end = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));
		$start = $end->modify('-1 month');
		$source = $this->load->database('prayer', TRUE);
		$this->load->model('Prayer_bonus_model');
		$employees = $this->db->get('employees')->result_array();
		$employee = NULL;
		foreach ($employees as $row) if ((int) $row['id'] === (int) $employee_id) $employee = $row;
		if (!$employee) throw new RuntimeException('Data karyawan tidak ditemukan.');
		$matches = array();
		foreach ($source->get('karyawan')->result_array() as $row) {
			if ($this->Prayer_bonus_model->resolve_employee_id($row, $employees) === (int) $employee_id) $matches[] = (int) $row['karyawan_id'];
		}
		if (count($matches) > 1) throw new RuntimeException('Lebih dari satu karyawan sumber cocok. Periksa identitas karyawan.');
		$result = array('period'=>$start->format('Y-m'), 'matched'=>count($matches) === 1, 'source_id'=>$matches ? $matches[0] : NULL,
			'overtime_minutes'=>0, 'overtime_days'=>0, 'overtime_pay'=>0,
			'attendance_days'=>0, 'daily_rate'=>(float) $employee['daily_allowance'], 'daily_pay'=>0);
		if (!$matches) return $result;
		$params = array($matches[0], $start->format('Y-m-d'), $end->format('Y-m-d'));
		$records = $source->query("SELECT tanggal, jam_mulai, jam_selesai, status FROM lembur_request
			WHERE karyawan_id = ? AND tanggal >= ? AND tanggal < ? AND status = 'approved'", $params)->result_array();
		$overtime = $this->calculate_overtime($records);
		$result = array_merge($result, $overtime);
		$attendance = $source->query('SELECT COUNT(DISTINCT tanggal) AS days FROM absensi_karyawan
			WHERE karyawan_id = ? AND tanggal >= ? AND tanggal < ? AND jam_masuk_real IS NOT NULL', $params)->row_array();
		$result['attendance_days'] = (int) $attendance['days'];
		$result['daily_pay'] = $result['attendance_days'] * $result['daily_rate'];
		return $result;
	}

	public function calculate_overtime($records)
	{
		$daily = array();
		foreach ($records as $row) {
			if ($row['status'] !== 'approved') continue;
			$start = !empty($row['jam_mulai']) ? strtotime($row['jam_mulai']) : FALSE;
			$end = !empty($row['jam_selesai']) ? strtotime($row['jam_selesai']) : FALSE;
			if ($start === FALSE || $end === FALSE || $end <= $start) {
				throw new RuntimeException('Jam lembur ACC tahap 2 belum lengkap/valid pada '.$row['tanggal']);
			}
			$minutes = (int) floor(($end - $start) / 60);
			$daily[$row['tanggal']] = ($daily[$row['tanggal']] ?? 0) + $minutes;
		}
		$amount = 0;
		foreach ($daily as $minutes) $amount += min(50000, round($minutes * 10000 / 60));
		return array('overtime_minutes'=>array_sum($daily), 'overtime_days'=>count($daily), 'overtime_pay'=>$amount);
	}
}
