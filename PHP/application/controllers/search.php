<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	public function index()
	{
		$this->load->database();
		$this->load->model('MY_DBmodel');
		$this->load->library('db_table');

		$this->load->view('header');

		$submit = 'Transaction ID:6,Laboratory:87';//$this->input->post('q');
		$table = 'patient_expenses';//$this->input->post('t');

		$this->load->view('search-form');

		if (!empty ($submit) && !empty ($table)){

			$result = $this->MY_DBmodel->find($submit, $table);
			$html = $this->MY_DBmodel->makeTable($result);

			$this->load->view('table_view', array('tablehtml'=>$html));
		}
		$this->load->view('footer');
	}

}
