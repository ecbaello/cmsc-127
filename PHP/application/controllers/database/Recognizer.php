<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recognizer extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper("csrf_helper");
		$this->load->library('registry');
	}

	public function index()
	{
		$this->load->view('header');
		
		$this->load->view('footer');
	}

	public function data() {
		$data = [
			'data' => $this->registry->notRegistered()
		];
		csrf_json_response($data);
	}

	public function add()
	{	
		$title = $this->input->post('title');
		$link = $this->input->post('link');

		$data = 
		[
			'success' => $this->bookmarks_model->newBookmark($title, $link)
		];

		csrf_json_response($data);
	}

	public function remove()
	{
		$title = $this->input->post('title');

		$data = 
		[
			'success' => $this->bookmarks_model->deleteBookmark($title)
		];

		csrf_json_response($data);
	}
}
