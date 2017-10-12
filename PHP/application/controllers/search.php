<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	public function index()
	{
		$this->load->model('database_model');

	}

	public function getFields($tableName)
	{
		return $this->db->list_fields($tableName);
	}

	public function getData($tableName)
	{
		return $this->db->get($tableName)->result_array();
	}

	public function makeTable($query)
	{
		$this->load->library('table');

		$fields = $query->list_fields();
		$headers = $this->database_model->convertFields($fields);

		$this->table->set_heading($headers);

		return $this->table->generate($query);
	}
}
