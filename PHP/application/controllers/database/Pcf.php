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

		$this->makeSelector($subtable, str_replace('\\','/',site_url($this->getAccessURL(__FILE__))));
		
		$this->load->view('table_view', ['url'=>current_url(), 'title'=>$this->model->ModelTitle.': '.$subtable]);

		$this->load->view('table_settings');
		
		$this->load->view('footer');

	}
	

}
