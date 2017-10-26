<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patientexp extends MY_DBcontroller {

	public function index()
	{
		$this->load->model('database_patient_expenses_model');

		$this->model = $this->database_patient_expenses_model;

		$input = $this->input;
		$link = current_url();
		
		$this->handleRequest();

		$request = $this->input->post(DB_GET);
		if ($request == BOOL_ON) {

			$this->makeTableHTML();
			
		} else {
			$this->load->view('header');

			$this->load->view('html', array('html'=>'<h2 class="view-title">'.$this->model->ModelTitle.'</h2>'));
			$this->makeTableHTML();
			
			$this->load->view('footer');
		}
	}
	
}
