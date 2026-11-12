<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller {

	public function index()
	{
		$data = array(
			'page_title' => 'Dashboard Payroll',
			'active_menu' => 'dashboard',
			'stats' => array(
				array('label' => 'Karyawan Aktif', 'value' => count($this->Employee_model->get_all()), 'icon' => 'fas fa-users', 'bg' => 'bg-info'),
				array('label' => 'Komponen Gaji', 'value' => count($this->Salary_component_model->get_all()), 'icon' => 'fas fa-wallet', 'bg' => 'bg-success'),
				array('label' => 'Payroll Tercatat', 'value' => count($this->Payroll_model->get_all()), 'icon' => 'fas fa-money-check-alt', 'bg' => 'bg-warning'),
				array('label' => 'Kasbon Aktif', 'value' => count($this->Kasbon_model->get_all()), 'icon' => 'fas fa-hand-holding-usd', 'bg' => 'bg-danger'),
			),
			'activities' => array(
				'Modul master data dan payroll sudah tersambung ke database MySQL.',
				'Login admin memakai session CI3 dan password hash.',
				'Form simpan, edit, dan hapus sudah tersedia untuk data utama.',
				'Skema SQL awal sudah disiapkan untuk pengembangan lanjutan.',
			),
		);

		$this->render('dashboard/index', $data);
	}
}
