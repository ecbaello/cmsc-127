<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index()
	{
		echo 'test';
		$this->load->model('database_model');
		$this->database_model->test_main();
		$data['sample'] = "hello world";
		$data['table'] = array
							(
								array(1, 2, 3),
								array(2, 3, 4)
							);
		$this->load->view('table_view', $data);
		
	}

	public function test1()
	{
		echo 'test output';
	}
}
