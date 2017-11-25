<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patientexp extends MY_DBcontroller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('database_patient_expenses_model');

		$this->model = $this->database_patient_expenses_model;
	}
	
}
