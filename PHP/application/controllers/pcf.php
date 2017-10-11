<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcf extends CI_Controller {

	public function index()
	{
		$this->load->model('database_model');
		$this->load->model('database_pcf_model');

		$this->load->view('header');

		$table = $this->input->get('t');
		$hasInput = !empty($this->input->get('s'));

		if ( empty($table) ) {
			echo 'Select table';
		} else {
			

			if ($this->database_pcf_model->checkExists($table)){
				if ($hasInput) {
					$this->takeInput ($table);
				}

				$this->loadTable($table);
				$this->makeInputHtml($table);
			}

			
		}


		$this->load->view('footer');
	}

	public function loadTable($table)
	{	
		$result = $this->database_pcf_model->getTypeTable($table);

		$data = array(
			'tablehtml' => $this->database_model->makeTable($result)
		);
		$this->load->view('table_view', $data);
		
	}

	public function makeInputHtml($table)
	{
		$this->load->helper('url');

		$fields = $this->database_pcf_model->getFieldAssociations();
		$link = 'http://'.current_url().'?t='.html_escape($table).'&s=y';

		$data = array(
			'fields' => $fields,
			'link' => $link
		);
		$this->load->view('form_generator', $data);

	}

	public function takeInput ($table) {
		$inputs = $this->database_pcf_model->getFields();
		$arr = array();
		foreach ($inputs as $input) {
			$value = $this->input->post($input);
			if (! empty($value) ) $arr[$input] = $value; 
		}
		$this->database_pcf_model->insertIntoTypeTable($table, $arr);
	}
}
