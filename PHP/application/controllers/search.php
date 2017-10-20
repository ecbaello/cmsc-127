<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	public function index()
	{
		$this->load->database();
		$this->load->library('db_table');

		$this->load->view('header');

		$submit = $this->input->get('q');
		$table = $this->input->get('t');

		$this->load->model($table);

		$this->load->view('search-form');

		if (!empty ($submit) && !empty ($table)){

			$result = $this->$table->find($submit);
			$html = $this->$table->makeTable($result);

			$this->load->view('table_view', array('tablehtml'=>$html));
		}
		$this->load->view('footer');
	}

}
