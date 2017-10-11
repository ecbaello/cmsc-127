<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcfrr extends CI_Controller {

	public function index()
	{
		$this->load->model('database_model');
		$this->load->model('database_pcfrr_model');
		$this->load->view('header');

		$data = array(
			'tablehtml' => $this->database_pcfrr_model->generateTable()
		);
		$this->load->view('table_view', $data);

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
