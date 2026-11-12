<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_pay_model extends CI_Model {
	public function for_employee($source_id, $period)
	{
		$result = array('addition'=>0, 'deduction'=>0, 'items'=>array());
		if (!$source_id) return $result;
		$start = new DateTimeImmutable($period.'-01');
		$end = $start->modify('+1 month');
		$source = $this->load->database('prayer', TRUE);
		$records = $source->query("SELECT transaksi_id, transaksi_tanggal, transaksi_jenis, transaksi_nominal,
			transaksi_kategori_nama, transaksi_keterangan FROM transaksi
			WHERE LOWER(relasi_tipe) = 'karyawan' AND relasi_id = ? AND transaksi_tanggal >= ? AND transaksi_tanggal < ?
			ORDER BY transaksi_tanggal, transaksi_id", array((int) $source_id, $start->format('Y-m-d'), $end->format('Y-m-d')))->result_array();
		foreach ($records as $row) {
			$text = (string) $row['transaksi_keterangan'];
			// Collective payroll payments are not individual employee claims.
			if (preg_match('/gaji\s+(?:(?:seluruh|semua)\s+)?karyawan|payroll|gaji.*(?:seluruh|semua)/i', $text)) continue;
			$effect = $row['transaksi_jenis'] === 'Pengeluaran' ? 'addition' : ($row['transaksi_jenis'] === 'Pemasukan' ? 'deduction' : NULL);
			if (!$effect) continue;
			$amount = (float) $row['transaksi_nominal'];
			if ($amount < 0) throw new RuntimeException('Nominal transaksi keuangan negatif: '.$row['transaksi_id']);
			$result[$effect] += $amount;
			$result['items'][] = array('key'=>$source->database.':transaksi:'.$row['transaksi_id'], 'date'=>$row['transaksi_tanggal'], 'description'=>$text, 'category'=>$row['transaksi_kategori_nama'], 'effect'=>$effect, 'amount'=>$amount);
		}
		// Approved claims without a posted transaction are included once; linked claims
		// are already represented by their financial transaction above.
		$claims = $source->query("SELECT id,tanggal,nominal,keterangan,kategori FROM reimbursement_request
			WHERE karyawan_id = ? AND tanggal >= ? AND tanggal < ? AND status = 'approved'
			AND (transaksi_id IS NULL OR transaksi_id = 0) ORDER BY tanggal,id",
			array((int) $source_id,$start->format('Y-m-d'),$end->format('Y-m-d')))->result_array();
		foreach ($claims as $row) {
			$amount = (float) $row['nominal'];
			if ($amount < 0) throw new RuntimeException('Nominal reimbursement negatif.');
			$result['addition'] += $amount;
			$result['items'][] = array('key'=>$source->database.':reimbursement:'.$row['id'], 'date'=>$row['tanggal'], 'description'=>$row['keterangan'], 'category'=>$row['kategori'], 'effect'=>'addition', 'amount'=>$amount);
		}
		return $result;
	}
}
