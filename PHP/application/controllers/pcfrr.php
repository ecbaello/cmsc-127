<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcfrr extends MY_DBarraycontroller {

	public function index()
	{
		$this->load->model('database_pcfrr_model');
		$this->model = $this->database_pcfrr_model;
		$this->load->model('database_pcf_field_association_model');
		
		$table = $this->loadSession();
		
		$link = current_url();
		
		if ( empty($table) ) {
			echo 'Select table';
		} else {
			if ($this->model->checkCategoryExists($table)){
				
				$fields = array();
				$query = $this->db->query('select field from pcf_field_association where pcf_name !="'.$table.'" and field not in (select field from pcf_field_association where pcf_name = "'.$table.'")')->result();
				foreach ($query as $field) {
					array_push($fields, $field->field);
				}
				$this->model->fieldsToHide = $fields;
				
				$this->handleRequest($table);

				$request = $this->input->post(DB_GET);
				if ($request == BOOL_ON) {
					$this->makeTableHTML($table);
					
				} else {
					$this->load->view('header');
					
					$this->load->view('html', array('html'=>
						'<h2 class="view-title">'.$this->model->ModelTitle.'</h2>'.$this->makeSelectorHTML($table)
					));
								
					$this->makeTableHTML($table);
					
					$this->load->view('footer');
				}
				

			}
			
		}

	}
}