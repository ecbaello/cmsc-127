<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	public function index()
	{
		$this->load->model('database_model');

		$submit = $this->input->get('q');
		$table = $this->input->get('t');
		if (!empty ($submit) && !empty ($table)){
			$queries = explode ( "," , $submit);

		}
		$this->load->view('header');
		$this->load->view('search-form');
		$this->load->view('footer');

	}

}
