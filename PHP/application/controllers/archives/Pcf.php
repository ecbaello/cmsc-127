<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pcf extends MY_Archarraycontroller {

	public function __construct()
	{
		parent::__construct();
		$this->filepath = __FILE__;
	}

}
