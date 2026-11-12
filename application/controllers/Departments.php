<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Departments extends Admin_Controller {

	public function index()
	{
		$data = array(
			'page_title' => 'Divisi dan Jabatan',
			'active_menu' => 'departments',
			'departments' => $this->Department_model->get_all(),
		);

		$this->render('departments/index', $data);
	}

	public function create()
	{
		$data = array(
			'page_title' => 'Tambah Divisi & Jabatan',
			'active_menu' => 'departments',
			'department' => NULL,
			'action' => 'departments/store',
		);

		$this->render('departments/form', $data);
	}

	public function store()
	{
		$this->_validate();
		$this->Department_model->insert($this->_collect_input());
		$this->session->set_flashdata('success', 'Data divisi dan jabatan berhasil ditambahkan.');
		redirect('departments');
	}

	public function edit($id)
	{
		$data = array(
			'page_title' => 'Edit Divisi & Jabatan',
			'active_menu' => 'departments',
			'department' => $this->Department_model->find($id),
			'action' => 'departments/update/'.$id,
		);

		$this->render('departments/form', $data);
	}

	public function update($id)
	{
		$this->_validate();
		$this->Department_model->update($id, $this->_collect_input());
		$this->session->set_flashdata('success', 'Data divisi dan jabatan berhasil diperbarui.');
		redirect('departments');
	}

	public function delete($id)
	{
		if ($this->Department_model->is_used($id))
		{
			$this->session->set_flashdata('error', 'Divisi atau jabatan masih dipakai data karyawan.');
			redirect('departments');
		}

		$this->Department_model->delete($id);
		$this->session->set_flashdata('success', 'Data divisi dan jabatan berhasil dihapus.');
		redirect('departments');
	}

	private function _validate()
	{
		$this->form_validation->set_rules('division_name', 'Divisi', 'required');
		$this->form_validation->set_rules('position_name', 'Jabatan', 'required');
		$this->form_validation->set_rules('allowance', 'Tunjangan', 'required|numeric');

		if (! $this->form_validation->run())
		{
			$this->session->set_flashdata('error', validation_errors());
			redirect($this->uri->segment(2) === 'store' ? 'departments/create' : 'departments/edit/'.$this->uri->segment(3));
		}
	}

	private function _collect_input()
	{
		return array(
			'division_name' => $this->input->post('division_name', TRUE),
			'position_name' => $this->input->post('position_name', TRUE),
			'allowance' => (float) $this->input->post('allowance', TRUE),
		);
	}
}
