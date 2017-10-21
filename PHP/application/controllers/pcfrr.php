<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcfrr extends MY_DBcontroller {

	public function index()
	{
		$this->load->model('database_pcfrr_model');

		$request = $this->input->post(DB_GET);
		if ($request == BOOL_ON) {

			$this->makeTableHTML();
			
		} else {
			$this->model = $this->database_pcfrr_model;

			$input = $this->input;
			$link = current_url();
			
			$this->handleRequest();
			
			$this->load->view('header');

			$data = array(
				'link' => $link,
				'pcf_names' => $this->model->db->query('select * from pcf_type_table')
			);
			$this->load->view('pcf_selector',$data);

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

	public function changePCF(){
		$this->load->library('session');
		
		if(!empty($this->input->post('t'))){
			$_SESSION['pcfname']=$this->input->post('t');
		}else if(!isset($_SESSION['pcfname'])){
			$_SESSION['pcfname']='General';
		}
		
		return $_SESSION['pcfname'];
	}
}
