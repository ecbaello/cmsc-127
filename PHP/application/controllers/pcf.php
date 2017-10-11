<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcf extends CI_Controller {

	public function index()
	{
		$this->load->model('database_model');
		$this->load->view('header');

		$table = $this->input->get('t');
		if ( empty($table) ) {
			echo 'Select table';
		} else
			$this->loadTable($table);

		$this->load->view('footer');
	}

	public function loadTable($table)
	{	
		$this->load->model('database_pcf_model');
		
		if ($this->database_pcf_model->checkExists($table)){
			
			$result = $this->database_pcf_model->getTypeTable($table);

			$data = array(
				'tablehtml' => $this->database_model->makeTable($result)
			);
			$this->load->view('table_view', $data);
		} else {
			echo 'table not found';
		}
		

		
	}

	

	
}
