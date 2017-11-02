<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcf extends MY_DBarraycontroller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('database_pcf_model');
		$this->model = $this->database_pcf_model;
	}

	protected function makeHTML($subtable)
	{
		
		$this->load->view('header');

		$this->load->view('html', array('html'=>
			'<h2 class="view-title">'.$this->model->ModelTitle.'</h2>'
		));
		
		$this->load->view('table_view');
		$this->load->view('footer');

	}
	

}
