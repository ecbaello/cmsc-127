<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index()
	{
		echo 'test';
		$this->load->model('database_model');
		$this->load->model('database_pcf_model');
		$this->load->model('database_pcfrr_model');

		$data = array(
			'pcf_particulars' => 'Hello Sample',
			'pcf_medical_supplies' => 4.5
		);
		$this->database_pcf_model->insertIntoTypeTable('General', $data);
		
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
}
