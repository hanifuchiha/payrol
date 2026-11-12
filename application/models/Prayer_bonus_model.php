<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prayer_bonus_model extends CI_Model {

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
		$leaders = $source->query(
			'SELECT k.karyawan_id, k.karyawan_nip, k.karyawan_nama, k.karyawan_telp,
			 COUNT(DISTINCT a.tanggal, a.jenis_sholat) AS total_absen
			 FROM absen_sholat a JOIN karyawan k ON k.karyawan_id = a.karyawan_id
			 WHERE a.tanggal >= ? AND a.tanggal < ?
			 GROUP BY k.karyawan_id, k.karyawan_nip, k.karyawan_nama, k.karyawan_telp
			 ORDER BY total_absen DESC, k.karyawan_id ASC LIMIT 3',
			array($start->format('Y-m-d'), $end->format('Y-m-d'))
		)->result_array();
		$employees = $this->db->get('employees')->result_array();
		return $this->calculate($employee_id, $start->format('Y-m'), $leaders, $employees);
	}

	public function calculate($employee_id, $period, $leaders, $employees)
	{
		$result = array('period' => $period, 'rank' => NULL, 'count' => 0, 'rate' => 5000, 'amount' => 0);
		foreach (array_slice($leaders, 0, 3) as $index => $leader) {
			if ($this->resolve_employee_id($leader, $employees) === (int) $employee_id) {
				if ($result['rank'] !== NULL) {
					throw new RuntimeException('Pencocokan penerima bonus sholat ambigu. Periksa nama, NIK, dan telepon karyawan.');
				}
				$result['rank'] = $index + 1;
				$result['count'] = (int) $leader['total_absen'];
				$result['amount'] = $result['count'] * $result['rate'];
			}
		}
		return $result;
	}

	public function resolve_employee_id($source, $employees)
	{
		$matches = array();
		$source_phone = $this->phone($source['karyawan_telp']);
		foreach ($employees as $employee) {
			if ((string) $employee['nik'] === 'PK-'.(int) $source['karyawan_id']
				|| (!empty($source['karyawan_nip']) && (string) $employee['nik'] === (string) $source['karyawan_nip'])
				|| ($this->name($employee['name']) !== '' && $this->name($employee['name']) === $this->name($source['karyawan_nama']))
				|| (strlen($source_phone) >= 10 && $this->phone($employee['phone']) === $source_phone)) {
				$matches[] = (int) $employee['id'];
			}
		}
		if (count($matches) > 1) throw new RuntimeException('Pencocokan karyawan sumber ambigu: '.$source['karyawan_nama']);
		return $matches ? $matches[0] : NULL;
	}

	private function phone($value)
	{
		$value = preg_replace('/\D+/', '', (string) $value);
		if (strpos($value, '0') === 0) return '62'.substr($value, 1);
		if (strpos($value, '8') === 0) return '62'.$value;
		return $value;
	}

	private function name($value)
	{
		return strtolower(preg_replace('/\s+/', ' ', trim((string) $value)));
	}
}
