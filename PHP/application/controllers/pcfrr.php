<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcfrr extends MY_DBarraycontroller {

	public function index()
	{
		$this->load->model('database_pcfrr_model');
		$this->model = $this->database_pcfrr_model;
		
		$table = $this->loadSession();
		
		$link = current_url();
		
		if ( empty($table) ) {
			echo 'Select table';
		} else {
			if ($this->model->checkCategoryExists($table)){
				$this->handleRequest($table);

				$request = $this->input->post(DB_GET);
				if ($request == BOOL_ON) {
					$this->makeTableHTML($table);
					
				} else {
					$this->load->view('header');
					$this->load->view('html', array('html'=>'<h2 class="view-title">'.$this->model->ModelTitle.'</h2>'));
					$data = array(
						'link' => current_url(),
						'pcf_names' => $this->model->db->query('select * from pcf_type_table')
					);
					$this->load->view('pcf_selector',$data);
								
					$this->makeTableHTML($table);
					
					$this->load->view('footer');
				}
				

			}
			
		}

	}
}