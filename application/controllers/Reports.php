<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Admin_Controller {

	public function index()
	{
		$data = array(
			'page_title' => 'Laporan dan Slip Gaji',
			'active_menu' => 'reports',
			'reports' => $this->Report_model->get_all(),
			'slips' => $this->Report_model->get_slips(),
			'kasbon_summaries' => $this->Kasbon_model->get_summary_by_employee(),
		);

		$this->render('reports/index', $data);
	}

	public function slip($id)
	{
		$data = array(
			'page_title' => 'Slip Gaji',
			'active_menu' => 'reports',
			'slip' => $this->Payroll_model->get_slip($id),
		);

		$this->render('reports/slip', $data);
	}

	public function slip_pdf($id)
	{
		$data = array(
			'slip' => $this->Payroll_model->get_slip($id),
		);

		$html = $this->load->view('reports/slip_pdf', $data, TRUE);
		$this->pdf->create($html, 'slip-gaji-'.$id.'.pdf');
	}
}
