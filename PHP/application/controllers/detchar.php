<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detchar extends MY_DBcontroller {

	public function index()
	{
		$this->load->model('database_detailed_charges_model');

		$this->model = $this->database_detailed_charges_model;

		$input = $this->input;
		$link = current_url();
		
		$this->handleRequest();

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
