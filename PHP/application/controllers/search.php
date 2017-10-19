<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	public function index()
	{
		$this->load->database();
		$this->load->model('search_model');
		$this->load->library('db_table');

		$this->load->view('header');

		$submit = $this->input->post('q');
		$table = 'detailed_charges';//$this->input->post('t');

		$this->load->view('search-form');
		$this->search_model->loadTable($table);

		if (!empty ($submit) && !empty ($table)){

			$result = $this->search_model->find($submit);
			$html = $this->search_model->makeTable($result);

			$this->load->view('table_view', array('tablehtml'=>$html));
		}
		$this->load->view('footer');
	}

}
