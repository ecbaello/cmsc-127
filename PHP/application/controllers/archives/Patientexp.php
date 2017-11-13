<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patientexp extends MY_Archcontroller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('archive_patient_expenses_model');
		$this->model = $this->archive_patient_expenses_model;
		$this->model->init();
	}
	
}
