<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcfrr extends MY_DBarraycontroller {


	public function __construct()
	{
		parent::__construct();
		$this->load->model('database_pcfrr_model');
		$this->model = $this->database_pcfrr_model;
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