<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	protected $page_title = 'Payroll App';

	public function __construct()
	{
		parent::__construct();
	}

	protected function render($view, $data = array())
	{
		$data['page_title'] = isset($data['page_title']) ? $data['page_title'] : $this->page_title;
		$data['current_user'] = $this->session->userdata('admin_name');
		$this->load->view('layouts/header', $data);
		$this->load->view('layouts/sidebar', $data);
		$this->load->view($view, $data);
		$this->load->view('layouts/footer');
	}
}

class Admin_Controller extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('Payroll_sso');
		$this->payroll_sso->enforce();
	}
}
