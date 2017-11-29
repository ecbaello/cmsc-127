<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Registry
{
	public function __construct()
	{
		$this->load->model('Registry_model');
	}
	public function __call($method, $arguments)
	{	
		return call_user_func_array( array($this->Registry_model, $method), $arguments);
	}

	public function __get($var)
	{
		return get_instance()->$var;
	}
}