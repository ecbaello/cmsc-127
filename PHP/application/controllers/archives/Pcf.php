<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcf extends MY_DBarraycontroller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('archive_pcf_model');
		
		$this->model = $this->archive_pcf_model;
		
		$this->model->init();
	}

}
