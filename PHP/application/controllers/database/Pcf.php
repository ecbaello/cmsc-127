<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcf extends MY_DBarraycontroller {

	public function __construct()
	{
		parent::__construct();
		$this->filepath = __FILE__;
		$this->load->model('database_pcf_model');
		$this->model = $this->database_pcf_model;
		$this->model->init();
	}

}
