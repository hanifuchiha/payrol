<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salary_components extends Admin_Controller {

	public function index()
	{
		$data = array(
			'page_title' => 'Komponen Gaji',
			'active_menu' => 'salary_components',
			'components' => $this->Salary_component_model->get_all(),
		);

		$this->render('salary_components/index', $data);
	}

	public function create()
	{
		$data = array(
			'page_title' => 'Tambah Komponen Gaji',
			'active_menu' => 'salary_components',
			'component' => NULL,
			'action' => 'salary_components/store',
		);

		$this->render('salary_components/form', $data);
	}

	public function store()
	{
		$this->_validate();
		$this->Salary_component_model->insert($this->_collect_input());
		$this->session->set_flashdata('success', 'Komponen gaji berhasil ditambahkan.');
		redirect('salary_components');
	}

	public function edit($id)
	{
		$data = array(
			'page_title' => 'Edit Komponen Gaji',
			'active_menu' => 'salary_components',
			'component' => $this->Salary_component_model->find($id),
			'action' => 'salary_components/update/'.$id,
		);

		$this->render('salary_components/form', $data);
	}

	public function update($id)
	{
		$this->_validate();
		$this->Salary_component_model->update($id, $this->_collect_input());
		$this->session->set_flashdata('success', 'Komponen gaji berhasil diperbarui.');
		redirect('salary_components');
	}

	public function delete($id)
	{
		$this->Salary_component_model->delete($id);
		$this->session->set_flashdata('success', 'Komponen gaji berhasil dihapus.');
		redirect('salary_components');
	}

	private function _validate()
	{
		$this->form_validation->set_rules('name', 'Nama Komponen', 'required');
		$this->form_validation->set_rules('component_type', 'Tipe', 'required');
		$this->form_validation->set_rules('default_amount', 'Nominal', 'required|numeric');

		if (! $this->form_validation->run())
		{
			$this->session->set_flashdata('error', validation_errors());
			redirect($this->uri->segment(2) === 'store' ? 'salary_components/create' : 'salary_components/edit/'.$this->uri->segment(3));
		}
	}

	private function _collect_input()
	{
		return array(
			'name' => $this->input->post('name', TRUE),
			'component_type' => $this->input->post('component_type', TRUE),
			'default_amount' => (float) $this->input->post('default_amount', TRUE),
		);
	}
}
