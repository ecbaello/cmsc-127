<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index()
	{
		echo 'test';
		$this->load->model('database_model');
		$model = $this->database_model;

		$data['table_header'] = $model->getFields($model::PCFTableName);
		$data['table'] = $model->getData($model::PCFTableName);
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
}
