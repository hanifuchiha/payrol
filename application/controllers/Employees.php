<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employees extends Admin_Controller {

	public function index()
	{
		$data = array(
			'page_title' => 'Data Karyawan',
			'active_menu' => 'employees',
			'employees' => $this->Employee_model->get_all(),
			'source_notice' => 'Gaji pokok dan uang harian per hari diatur di Data Karyawan. Payroll menghitung uang harian otomatis berdasarkan hari hadir bulan sebelumnya.',
		);

		$this->render('employees/index', $data);
	}

	public function create()
	{
		$data = array(
			'page_title' => 'Tambah Karyawan',
			'active_menu' => 'employees',
			'employee' => NULL,
			'departments' => $this->get_employee_departments(),
			'action' => 'employees/store',
		);

		$this->render('employees/form', $data);
	}

	public function store()
	{
		$this->_validate();
		$this->Employee_model->insert($this->_collect_input());
		$this->session->set_flashdata('success', 'Data karyawan berhasil ditambahkan.');
		redirect('employees');
	}

	public function edit($id)
	{
		$data = array(
			'page_title' => 'Edit Karyawan',
			'active_menu' => 'employees',
			'employee' => $this->Employee_model->find($id),
			'departments' => $this->get_employee_departments(),
			'action' => 'employees/update/'.$id,
		);

		$this->render('employees/form', $data);
	}

	public function update($id)
	{
		$this->_validate($id);
		$this->Employee_model->update($id, $this->_collect_input());
		$this->session->set_flashdata('success', 'Data karyawan berhasil diperbarui.');
		redirect('employees');
	}

	public function delete($id)
	{
		$this->Employee_model->delete($id);
		$this->session->set_flashdata('success', 'Data karyawan berhasil dihapus.');
		redirect('employees');
	}

	private function _validate($id = NULL)
	{
		$current = $id ? $this->Employee_model->find($id) : NULL;
		$nikRule = 'trim';
		if (trim((string) $this->input->post('nik', TRUE)) !== '' && (! $current || (string) $current['nik'] !== trim((string) $this->input->post('nik', TRUE))))
		{
			$nikRule .= '|is_unique[employees.nik]';
		}

		$this->form_validation->set_rules('nik', 'NIK', $nikRule);
		$this->form_validation->set_rules('name', 'Nama', 'trim|max_length[120]');
		$this->form_validation->set_rules('phone', 'Nomor Telepon', 'trim|max_length[30]');
		$this->form_validation->set_rules('department_id', 'Divisi & Jabatan', 'trim|integer|greater_than[0]');
		$this->form_validation->set_rules('base_salary', 'Gaji Pokok', 'trim|numeric|greater_than_equal_to[0]');
		$this->form_validation->set_rules('daily_allowance', 'Uang Harian per Hari', 'trim|numeric|greater_than_equal_to[0]');
		$this->form_validation->set_rules('employment_status', 'Status Kerja', 'trim|in_list[Tetap,Kontrak,PKL]');
		$this->form_validation->set_rules('npwp', 'NPWP', 'trim');
		$this->form_validation->set_rules('bpjs', 'BPJS', 'trim');
		$this->form_validation->set_rules('join_date', 'Tanggal Masuk', 'trim');

		$employmentStatus = (string) $this->input->post('employment_status', TRUE);
		if ($employmentStatus === 'Kontrak')
		{
			$this->form_validation->set_rules('contract_start_date', 'Tanggal Awal Kontrak', 'trim');
			$this->form_validation->set_rules('contract_end_date', 'Tanggal Akhir Kontrak', 'trim');
		}

		if (! $this->form_validation->run())
		{
			$this->session->set_flashdata('error', validation_errors());
			redirect($id ? 'employees/edit/'.$id : 'employees/create');
		}
	}

	private function _collect_input()
	{
		return array(
			'nik' => $this->normalize_nullable($this->input->post('nik', TRUE)),
			'name' => $this->normalize_nullable($this->input->post('name', TRUE)),
			'phone' => $this->Employee_model->normalize_phone($this->input->post('phone', TRUE)),
			'npwp' => $this->normalize_nullable($this->input->post('npwp', TRUE)),
			'bpjs' => $this->normalize_nullable($this->input->post('bpjs', TRUE)),
			'department_id' => $this->normalize_nullable($this->input->post('department_id', TRUE)),
			'base_salary' => $this->normalize_salary($this->input->post('base_salary', TRUE)),
			'daily_allowance' => $this->normalize_salary($this->input->post('daily_allowance', TRUE)),
			'employment_status' => $this->normalize_nullable($this->input->post('employment_status', TRUE)),
			'join_date' => $this->normalize_nullable($this->input->post('join_date', TRUE)),
			'contract_start_date' => $this->normalize_contract_date('contract_start_date'),
			'contract_end_date' => $this->normalize_contract_date('contract_end_date'),
		);
	}

	private function get_employee_departments()
	{
		return $this->Department_model->get_all();
	}

	private function normalize_contract_date($field)
	{
		$status = (string) $this->input->post('employment_status', TRUE);
		if ($status !== 'Kontrak') {
			return NULL;
		}

		return $this->normalize_nullable($this->input->post($field, TRUE));
	}

	private function normalize_nullable($value)
	{
		$value = trim((string) $value);
		return $value === '' ? NULL : $value;
	}

	private function normalize_salary($value)
	{
		$value = trim((string) $value);
		if ($value === '') {
			return 0;
		}

		if (preg_match('/^\d+(\.\d+)?$/', $value)) {
			return (float) round((float) $value);
		}

		$digits = preg_replace('/\D+/', '', $value);
		return $digits === '' ? 0 : (float) $digits;
	}
}
