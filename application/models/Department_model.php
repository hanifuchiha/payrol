<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department_model extends CI_Model {

	public function get_all()
	{
		return $this->db->order_by('division_name', 'ASC')
			->order_by('position_name', 'ASC')
			->get('departments')
			->result_array();
	}

	public function get_dropdown()
	{
		return $this->db->order_by('division_name', 'ASC')
			->order_by('position_name', 'ASC')
			->get('departments')
			->result_array();
	}

	public function find($id)
	{
		return $this->db->get_where('departments', array('id' => $id))->row_array();
	}

	public function insert($data)
	{
		return $this->db->insert('departments', $data);
	}

	public function update($id, $data)
	{
		return $this->db->where('id', $id)->update('departments', $data);
	}

	public function delete($id)
	{
		return $this->db->delete('departments', array('id' => $id));
	}

	public function is_used($id)
	{
		return $this->db->where('department_id', $id)->count_all_results('employees') > 0;
	}
}
