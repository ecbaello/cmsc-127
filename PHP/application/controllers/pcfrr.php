<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcfrr extends CI_Controller {

	public function index()
	{
		$this->load->model('database_model');
		$this->load->model('database_pcfrr_model');
		
		$this->load->view('header');

		$query = $this->database_pcfrr_model->getTable();

		$data = array(
			'tablehtml' => $this->database_model->makeTable($query)
		);
		$this->load->view('table_view', $data);

		$this->load->view('footer');
	}
	
}
