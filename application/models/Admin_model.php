<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

	public function find_by_username($username)
	{
		return $this->db->get_where('admins', array('username' => $username))->row_array();
	}

	public function verify($username, $password)
	{
		$admin = $this->find_by_username($username);

		if (! $admin || ! password_verify($password, $admin['password']))
		{
			return FALSE;
		}

		return $admin;
	}

	public function insert($data)
	{
		return $this->db->insert('admins', $data);
	}
}
