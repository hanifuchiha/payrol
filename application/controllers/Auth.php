<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Auth extends CI_Controller {
    public function login() {
        $this->load->library('Payroll_sso');
        $this->payroll_sso->start();
    }
    public function attempt() { $this->login(); }
    public function sso_callback() {
        $this->load->library('Payroll_sso');
        $this->payroll_sso->callback();
    }
    public function logout() {
        $this->session->sess_destroy();
        redirect(Payroll_sso::BASE.'logout','location',303);
    }
    public function sso_logout() {
        $this->session->sess_destroy();
        $this->output->set_content_type('text/html', 'utf-8')->set_output('<!doctype html><html><body>OK</body></html>');
    }
    public function __construct() {
        parent::__construct();
        $this->load->library('Payroll_sso');
    }
}
