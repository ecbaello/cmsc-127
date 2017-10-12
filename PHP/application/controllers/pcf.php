<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcf extends CI_Controller {

	public function index()
	{
		$this->load->model('database_model');
		$this->load->model('database_pcf_model');

		$this->load->view('header');

		$table = $this->input->get('t');
		$submit = $this->input->get('s');

		if ( empty($table) ) {
			echo 'Select table';
		} else {
			if ($this->database_pcf_model->checkExists($table)){
				if ($submit == 'i') {
					$this->takeInput ($table);
				} else if ($submit == 'r') {
					$submit = $this->input->get('id');
					if ( !empty($submit) ) $this->database_pcf_model->deleteWithPK($submit);
				}

				$this->loadTable($table);
				$this->makeInputHtml($table);
			}
			
		}


		$this->load->view('footer');
	}

	public function loadTable($subtable)
	{	
		$result = $this->database_pcf_model->getTypeTable($subtable);

		$data = array(
			'tablehtml' => $this->makeTableWithDeleteAndResult($subtable, $result, 'pcf_id', $this->getLink())
		);
		$this->load->view('table_view', $data);
		
	}

	public function makeTableWithDeleteAndResult($subtable, $table, $pk, $link)
	{
		$this->load->library('db_table');

		$query = $table;

		$fields = $query->list_fields();
		$headers = $this->database_model->convertFields($fields);

		$this->db_table->set_heading($headers);

		return $this->db_table->generateDBUsingPK($query, $pk, $link, $subtable);
	}

	public function getLink() {
		return 'http://'.current_url();
	}

	public function makeInputHtml($table)
	{
		$this->load->helper('url');

		$fields = $this->database_pcf_model->getFieldAssociations();
		$link = $this->getLink().'?t='.html_escape($table).'&s=i';

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
