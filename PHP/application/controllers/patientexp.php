<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patientexp extends MY_DBcontroller {

	public function index()
	{
		$this->load->model('database_patient_expenses_model');

		$this->model = $this->database_patient_expenses_model;

		$input = $this->input;
		$link = current_url();
		
		$this->handleRequest();

		$request = $this->input->post(DB_GET);
		if ($request == BOOL_ON) {

			$this->makeTableHTML();
			
		} else {
			$this->load->view('header');

			$this->makeTableHTML();
			$form = $this->makeInputHtml(true);

			$modal = array(
				'actiontitle' => 'Input a row',
				'modalid' => 'input-form',
				'modalcontent' => $form
			);
			$this->load->view('popup_generator', $modal);

			$this->load->view('footer');
		}
	}
	
}
