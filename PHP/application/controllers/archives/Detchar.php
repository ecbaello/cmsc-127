<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detchar extends MY_DBcontroller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('archive_detailed_charges_model');

		$this->model = $this->archive_detailed_charges_model;

		$this->model->init();
	}
	
	
}
