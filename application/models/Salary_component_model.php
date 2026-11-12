<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salary_component_model extends CI_Model {

	public function get_all()
	{
		return $this->db->order_by('component_type', 'ASC')
			->order_by('name', 'ASC')
			->get('salary_components')
			->result_array();
	}

	public function find($id)
	{
		return $this->db->get_where('salary_components', array('id' => $id))->row_array();
	}

	public function insert($data)
	{
		return $this->db->insert('salary_components', $data);
	}

	public function update($id, $data)
	{
		return $this->db->where('id', $id)->update('salary_components', $data);
	}

	public function delete($id)
	{
		return $this->db->delete('salary_components', array('id' => $id));
	}
}
