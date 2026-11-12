<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payrolls extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
	}

	public function prayer_bonus()
	{
		try {
			$this->load->model('Prayer_bonus_model');
			$result = $this->Prayer_bonus_model->for_employee(
				(int) $this->input->get('employee_id', TRUE),
				(int) $this->input->get('month', TRUE),
				(int) $this->input->get('year', TRUE)
			);
			$this->load->model('Attendance_pay_model');
			$attendance = $this->Attendance_pay_model->for_employee((int) $this->input->get('employee_id', TRUE), (int) $this->input->get('month', TRUE), (int) $this->input->get('year', TRUE));
			$this->load->model('Finance_pay_model');
			$result['attendance'] = $attendance;
			$result['finance'] = $this->Finance_pay_model->for_employee($attendance['source_id'], $attendance['period']);
			$this->output->set_content_type('application/json')->set_output(json_encode($result));
		} catch (Exception $e) {
			$this->output->set_status_header(422)->set_content_type('application/json')
				->set_output(json_encode(array('error' => $e->getMessage())));
		}
	}


	public function kasbon_total()
	{
		$employee_id = (int) $this->input->get('employee_id', TRUE);
		$payroll_id = $this->input->get('payroll_id', TRUE);
		$exclude_payroll_id = $payroll_id !== NULL && $payroll_id !== '' ? (int) $payroll_id : NULL;
		$total = $this->Kasbon_model->get_total_by_employee($employee_id);
		$paid = $this->Kasbon_model->get_paid_by_employee($employee_id, $exclude_payroll_id);
		$remaining = $this->Kasbon_model->get_remaining_by_employee($employee_id, $exclude_payroll_id);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(array(
				'total' => $total,
				'paid' => $paid,
				'remaining' => $remaining,
			)));
	}

	public function index()
	{
		$month = (int) ($this->input->get('month', TRUE) ?: date('n'));
		$year = (int) ($this->input->get('year', TRUE) ?: date('Y'));
		if ($month < 1 || $month > 12) $month = (int) date('n');
		if ($year < 1900 || $year > 9998) $year = (int) date('Y');
		$this->load->model('Payroll_calculator_model');
		$rows = $this->Payroll_model->get_all($month, $year);
		$saved = array();
		foreach ($rows as $row) $saved[(int) $row['employee_id']] = TRUE;
		$errors = array();
		foreach ($this->Employee_model->get_all() as $employee) {
			if (isset($saved[(int) $employee['id']])) continue;
			try {
				$row = $this->Payroll_calculator_model->calculate((int) $employee['id'], $month, $year);
				$row['id'] = NULL;
				$row['employee_name'] = $employee['name'];
				$row['status'] = 'Belum disimpan';
				$rows[] = $row;
			} catch (Exception $e) {
				$errors[] = $employee['name'].': '.$e->getMessage();
			}
		}
		usort($rows, function ($a, $b) { return strcasecmp($a['employee_name'], $b['employee_name']); });
		$summary = array('period'=>sprintf('%02d/%04d', $month, $year), 'total_employee'=>count($rows), 'total_gross'=>0, 'total_net'=>0);
		foreach ($rows as $row) { $summary['total_gross'] += $row['gross_salary']; $summary['total_net'] += $row['net_salary']; }
		$data = array(
			'page_title' => 'Proses Payroll',
			'active_menu' => 'payrolls',
			'selected_month' => $month,
			'selected_year' => $year,
			'payrolls' => $rows,
			'calculation_errors' => $errors,
			'summary' => $summary,
		);

		$this->render('payrolls/index', $data);
	}

	public function create()
	{
		$data = array(
			'page_title' => 'Tambah Payroll',
			'active_menu' => 'payrolls',
			'payroll' => NULL,
			'default_employee' => (int) $this->input->get('employee_id', TRUE),
			'default_month' => max(1, min(12, (int) ($this->input->get('month', TRUE) ?: date('n')))),
			'default_year' => max(1900, min(9998, (int) ($this->input->get('year', TRUE) ?: date('Y')))),
			'employees' => $this->Employee_model->get_all(),
			'action' => 'payrolls/store',
			'current_kasbon_remaining' => 0,
		);

		$this->render('payrolls/form', $data);
	}

	public function store()
	{
		$this->require_post();
		$this->_validate();
		$data = $this->collect_payroll_input('payrolls/create');
		if (! $this->Payroll_model->insert($data)) {
			$this->session->set_flashdata('error', 'Payroll gagal disimpan: data sudah disetujui/berubah, cicilan melebihi saldo kasbon, atau transaksi keuangan sudah dipakai.');
			redirect('payrolls/create');
		}
		$this->session->set_flashdata('success', 'Data payroll berhasil ditambahkan.');
		redirect('payrolls?month='.$data['payroll_month'].'&year='.$data['payroll_year']);
	}

	public function edit($id)
	{
		$this->require_editable_payroll($id);
		$data = array(
			'page_title' => 'Edit Payroll',
			'active_menu' => 'payrolls',
			'payroll' => $this->Payroll_model->find($id),
			'employees' => $this->Employee_model->get_all(),
			'action' => 'payrolls/update/'.$id,
			'current_kasbon_remaining' => 0,
		);

		if (!empty($data['payroll']['employee_id'])) {
			$data['current_kasbon_remaining'] = $this->Kasbon_model->get_remaining_by_employee((int) $data['payroll']['employee_id'], (int) $id);
		}

		$this->render('payrolls/form', $data);
	}

	public function update($id)
	{
		$this->require_post();
		$this->require_editable_payroll($id);
		$this->_validate($id);
		$data = $this->collect_payroll_input('payrolls/edit/'.$id);
		if (! $this->Payroll_model->update($id, $data)) {
			$this->session->set_flashdata('error', 'Payroll gagal disimpan: data sudah disetujui/berubah, cicilan melebihi saldo kasbon, atau transaksi keuangan sudah dipakai.');
			redirect('payrolls/edit/'.$id);
		}
		$this->session->set_flashdata('success', 'Data payroll berhasil diperbarui.');
		redirect('payrolls?month='.$data['payroll_month'].'&year='.$data['payroll_year']);
	}

	public function send_whatsapp($id)
	{
		$this->require_post();
		$slip = $this->Payroll_model->get_slip((int) $id);
		if (empty($slip)) {
			$this->session->set_flashdata('error', 'Data payroll tidak ditemukan.');
			redirect('payrolls');
		}
		if ((string) ($slip['status'] ?? '') !== 'Disetujui') {
			$this->session->set_flashdata('error', 'WhatsApp hanya bisa dikirim untuk payroll yang sudah Disetujui.');
			redirect('payrolls');
		}

		$phone = $this->Employee_model->normalize_phone($slip['phone'] ?? '');
		if ($phone === '') {
			$this->session->set_flashdata('error', 'Nomor WhatsApp karyawan belum tersedia.');
			redirect('payrolls');
		}

		$pdfPath = $this->build_slip_pdf_file($slip);
		if ($pdfPath === '') {
			$this->session->set_flashdata('error', 'Gagal menyiapkan slip PDF.');
			redirect('payrolls');
		}

		$message = $this->build_payroll_approval_message($slip);
		$sent = $this->send_fonnte_with_attachment($phone, $message, $pdfPath);
		@unlink($pdfPath);

		if (!$sent['ok']) {
			$this->session->set_flashdata('error', 'Gagal kirim WhatsApp: ' . $sent['error']);
			redirect('payrolls');
		}

		$this->session->set_flashdata('success', 'Slip gaji berhasil dikirim ke WhatsApp karyawan.');
		redirect('payrolls');
	}

	public function delete($id)
	{
		$this->require_post();
		$this->require_editable_payroll($id);
		if (!$this->Payroll_model->delete($id)) {
			$this->session->set_flashdata('error', 'Payroll tidak dapat dihapus; data sudah disetujui atau berubah.');
			redirect('payrolls');
			return;
		}
		$this->session->set_flashdata('success', 'Data payroll berhasil dihapus.');
		redirect('payrolls');
	}

	private function _validate($id = NULL)
	{
		$this->form_validation->set_rules('employee_id', 'Karyawan', 'required|integer');
		$this->form_validation->set_rules('payroll_month', 'Bulan', 'required|integer|greater_than_equal_to[1]|less_than_equal_to[12]');
		$this->form_validation->set_rules('payroll_year', 'Tahun', 'required|integer|greater_than_equal_to[1900]|less_than_equal_to[9998]');
		$this->form_validation->set_rules('bonus', 'Bonus', 'required|numeric|greater_than_equal_to[0]|regex_match[/^\d+(\.\d+)?$/D]');
		$this->form_validation->set_rules('deduction', 'Potongan', 'required|numeric|greater_than_equal_to[0]|regex_match[/^\d+(\.\d+)?$/D]');
		$this->form_validation->set_rules('kasbon', 'Kasbon', 'required|numeric|greater_than_equal_to[0]|regex_match[/^\d+(\.\d+)?$/D]');
		$this->form_validation->set_rules('status', 'Status', 'required|in_list[Draft,Diproses,Disetujui]');

		$employee_id = (int) $this->input->post('employee_id', TRUE);
		$payroll_month = (int) $this->input->post('payroll_month', TRUE);
		$payroll_year = (int) $this->input->post('payroll_year', TRUE);

		if (! $this->form_validation->run())
		{
			$this->session->set_flashdata('error', validation_errors());
			redirect($id === NULL ? 'payrolls/create' : 'payrolls/edit/'.$id);
		}

		if (! $this->Employee_model->find($employee_id))
		{
			$this->session->set_flashdata('error', 'Data karyawan tidak ditemukan.');
			redirect($id === NULL ? 'payrolls/create' : 'payrolls/edit/'.$id);
		}

		if ($this->Payroll_model->exists_for_period($employee_id, $payroll_month, $payroll_year, $id))
		{
			$this->session->set_flashdata('error', 'Payroll untuk karyawan dan periode tersebut sudah ada.');
			redirect($id === NULL ? 'payrolls/create' : 'payrolls/edit/'.$id);
		}

		$remainingKasbon = $this->Kasbon_model->get_remaining_by_employee($employee_id, $id);
		$kasbonInput = $this->normalize_currency($this->input->post('kasbon', TRUE));
		if ($kasbonInput < 0 || $kasbonInput > $remainingKasbon)
		{
			$this->session->set_flashdata('error', 'Cicilan kasbon melebihi sisa hutang karyawan.');
			redirect($id === NULL ? 'payrolls/create' : 'payrolls/edit/'.$id);
		}
	}

	private function collect_payroll_input($return_url)
	{
		try {
			return $this->_collect_input();
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'Payroll gagal dihitung: '.$e->getMessage());
			redirect($return_url);
			exit;
		}
	}

	private function _collect_input()
	{
		$this->load->model('Payroll_calculator_model');
		return $this->Payroll_calculator_model->calculate(
			(int) $this->input->post('employee_id', TRUE),
			(int) $this->input->post('payroll_month', TRUE),
			(int) $this->input->post('payroll_year', TRUE),
			$this->normalize_currency($this->input->post('bonus', TRUE)),
			$this->normalize_currency($this->input->post('deduction', TRUE)),
			$this->normalize_currency($this->input->post('kasbon', TRUE)),
			$this->input->post('status', TRUE)
		);
	}

	private function require_post()
	{
		if ($this->input->method(TRUE) !== 'POST') {
			$this->output->set_header('Allow: POST');
			show_error('Gunakan formulir untuk menjalankan aksi ini.', 405);
			exit;
		}
	}

	private function require_editable_payroll($id)
	{
		$payroll = $this->Payroll_model->find((int) $id);
		if (!$payroll) { show_404(); exit; }
		if ($payroll['status'] === 'Disetujui') {
			show_error('Payroll yang sudah Disetujui tidak dapat diubah atau dihapus.', 409);
			exit;
		}
	}

	private function normalize_currency($value)
	{
		$value = trim((string) $value);
		if ($value === '') return 0;
		if (!preg_match('/^\d+(\.\d+)?$/D', $value) || !is_finite((float) $value)) {
			throw new InvalidArgumentException('Nominal harus berupa angka nol atau positif.');
		}
		return (float) round((float) $value);
	}

	private function build_slip_pdf_file($slip)
	{
		$html = $this->load->view('reports/slip_pdf', array('slip' => $slip), TRUE);
		$pdfBinary = $this->pdf->create(
			$html,
			'slip-gaji-' . (int) ($slip['id'] ?? 0) . '.pdf',
			'A4',
			'portrait',
			FALSE
		);
		if (empty($pdfBinary)) {
			return '';
		}

		$tempPath = tempnam(sys_get_temp_dir(), 'slip_payroll_');
		if ($tempPath === FALSE) {
			return '';
		}

		$pdfPath = $tempPath . '.pdf';
		if (!@rename($tempPath, $pdfPath)) {
			@unlink($tempPath);
			$pdfPath = $tempPath;
		}

		if (@file_put_contents($pdfPath, $pdfBinary) === FALSE) {
			@unlink($pdfPath);
			return '';
		}

		return $pdfPath;
	}

	private function build_payroll_approval_message($slip)
	{
		$this->config->load('fonnte');
		$cfg = $this->config->item('fonnte');
		$companyName = $cfg['company_name'] ?? 'Perusahaan';

		return "Halo " . (string) ($slip['employee_name'] ?? 'Karyawan') . ",\n\n"
			. "Slip gaji Anda untuk periode "
			. str_pad((string) ($slip['payroll_month'] ?? ''), 2, '0', STR_PAD_LEFT)
			. '/' . (string) ($slip['payroll_year'] ?? '')
			. " telah disetujui.\n"
			. "Potongan kasbon periode ini: Rp " . number_format((float) ($slip['kasbon'] ?? 0), 0, ',', '.') . "\n"
			. "Sisa kasbon: Rp " . number_format(max(0, (float) ($slip['sisa_kasbon'] ?? 0)), 0, ',', '.') . "\n"
			. "Gaji bersih: Rp " . number_format((float) ($slip['net_salary'] ?? 0), 0, ',', '.') . "\n\n"
			. "File slip PDF terlampir.\n\n"
			. "Terima kasih.\n"
			. $companyName;
	}

	private function send_fonnte_with_attachment($phone, $message, $filePath)
	{
		$this->config->load('fonnte');
		$cfg = $this->config->item('fonnte');
		$token = $cfg['token'] ?? '';
		if (empty($token)) {
			return array('ok' => false, 'error' => 'Token Fonnte belum diisi di config.');
		}
		if (!is_file($filePath)) {
			return array('ok' => false, 'error' => 'File PDF tidak ditemukan.');
		}

		$payload = array(
			'target' => $phone,
			'message' => $message,
			'file' => curl_file_create($filePath, 'application/pdf', basename($filePath))
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.fonnte.com/send');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: ' . $token
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);

		$response = curl_exec($ch);
		$err = curl_error($ch);
		$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if (!empty($err)) {
			return array('ok' => false, 'error' => $err);
		}
		if ($code < 200 || $code >= 300) {
			return array('ok' => false, 'error' => 'HTTP ' . $code . ' | ' . (string) $response);
		}

		$json = json_decode($response, true);
		if (is_array($json) && isset($json['status']) && $json['status'] !== true) {
			$detail = $json['detail'] ?? 'status=false';
			return array('ok' => false, 'error' => (string) $detail);
		}

		return array('ok' => true, 'response' => $response);
	}
}
