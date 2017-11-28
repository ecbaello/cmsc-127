<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcfrr extends MY_DBarraycontroller {
	public function __construct()
	{
		parent::__construct(__file__);
		$this->load->model('database_pcfrr_model');
		$this->model = $this->database_pcfrr_model;
	}
}