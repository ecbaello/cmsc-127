<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index()
	{
		$this->load->model('database_model');
		$this->load->model('database_pcf_model');
		$this->load->model('database_pcfrr_model');

		$this->load->library('table');
		$result = $this->database_pcf_model->getTypeTable('General');

		$data = array(
			'tablehtml' => $this->makeTable($result)
		);
		$this->load->view('table_view', $data);
		
	}

	public function test1()
	{
		echo 'test output';
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
		$fields = $query->list_fields();
		$headers = $this->database_model->convertFields($fields);

		$this->table->set_heading($headers);

		return $this->table->generate($query);
	}
}
