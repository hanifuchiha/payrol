<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kasbons extends Admin_Controller {

	public function index()
	{
		$data = array(
			'page_title' => 'Kasbon Karyawan',
			'active_menu' => 'kasbons',
			'kasbons' => $this->Kasbon_model->get_all(),
		);

		$this->render('kasbons/index', $data);
	}

	public function create()
	{
		$data = array(
			'page_title' => 'Tambah Kasbon',
			'active_menu' => 'kasbons',
			'kasbon' => NULL,
			'employees' => $this->Employee_model->get_all(),
			'action' => 'kasbons/store',
		);

		$this->render('kasbons/form', $data);
	}

	public function store()
	{
		$this->_validate();
		$this->Kasbon_model->insert($this->_collect_input());
		$this->session->set_flashdata('success', 'Data kasbon berhasil ditambahkan.');
		redirect('kasbons');
	}

	public function edit($id)
	{
		$data = array(
			'page_title' => 'Edit Kasbon',
			'active_menu' => 'kasbons',
			'kasbon' => $this->Kasbon_model->find($id),
			'employees' => $this->Employee_model->get_all(),
			'action' => 'kasbons/update/'.$id,
		);

		$this->render('kasbons/form', $data);
	}

	public function update($id)
	{
		$this->_validate();
		$this->Kasbon_model->update($id, $this->_collect_input());
		$this->session->set_flashdata('success', 'Data kasbon berhasil diperbarui.');
		redirect('kasbons');
	}

	public function delete($id)
	{
		$this->Kasbon_model->delete($id);
		$this->session->set_flashdata('success', 'Data kasbon berhasil dihapus.');
		redirect('kasbons');
	}

	private function _validate()
	{
		$this->form_validation->set_rules('employee_id', 'Karyawan', 'required|integer');
		$this->form_validation->set_rules('transaction_date', 'Tanggal Kasbon', 'required');
		$this->form_validation->set_rules('amount', 'Nominal Kasbon', 'required|numeric');

		if (! $this->form_validation->run())
		{
			$this->session->set_flashdata('error', validation_errors());
			redirect($this->uri->segment(2) === 'store' ? 'kasbons/create' : 'kasbons/edit/'.$this->uri->segment(3));
		}
	}

	private function _collect_input()
	{
		return array(
			'employee_id' => (int) $this->input->post('employee_id', TRUE),
			'transaction_date' => $this->input->post('transaction_date', TRUE),
			'amount' => (float) $this->input->post('amount', TRUE),
			'note' => $this->input->post('note', TRUE),
		);
	}
}
