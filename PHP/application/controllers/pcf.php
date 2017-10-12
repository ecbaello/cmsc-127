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
				$this->handleRequest($submit, $table);

				$this->loadTable($table);
				$form = $this->makeInputHtml($table, true);

				$modal = array(
					'actiontitle' => 'Input a row',
					'modalid' => 'input-form',
					'modalcontent' => $form
				);
				$this->load->view('popup_generator', $modal);

			}
			
		}


		$this->load->view('footer');
	}

	public function handleRequest($submit, $table)
	{
		if ($submit == 'i') {
			$this->takeInput ($table);
		} else if ($submit == 'r') {
			$submit = $this->input->get('id');
			if ( !empty($submit) ) $this->database_pcf_model->deleteWithPK($submit);
		}
	}

	public function loadTable($subtable)
	{	
		$result = $this->database_pcf_model->getTypeTable($subtable);

		$data = array(
			'tablehtml' => $this->makePCFTableWithDelete($subtable, $result, 'pcf_id', current_url())
		);
		$this->load->view('table_view', $data);
		
	}

	public function makePCFTableWithDelete($subtable, $table, $pk, $link)
	{
		$this->load->library('db_table');

		$query = $table;

		$fields = $query->list_fields();
		$headers = $this->database_model->convertFields($fields);

		$this->db_table->set_heading($headers);

		return $this->db_table->generateDBUsingPK($query, $pk, $link, $subtable);
	}

	public function makeInputHtml($table, $isGet = false)
	{
		$this->load->helper('url');


		$fields = $this->database_pcf_model->getFieldAssociations();
		$link = current_url().'?t='.html_escape($table).'&s=i';

		$data = array(
			'fields' => $fields,
			'link' => $link
		);
		return $this->load->view('form_generator', $data, $isGet);

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
