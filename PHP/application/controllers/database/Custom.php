<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom extends CI_Controller {

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->model('registry_model');
	}

	public function load() {
		$arguments = func_get_args();

		$table_name = isset($arguments[0])?$arguments[0]:null;

		if (empty($table_name)) {
			show_404();
			return;
		}

		unset($arguments[0]);

		if (isset($arguments[1])) $method = $arguments[1];
		else $method = 'index';
		unset($arguments[1]);

		defined('NAV_SELECT') or define('NAV_SELECT', 1);
		$custom = $this->registry_model->customtable($table_name);

		$custom = $custom->row();

		if (empty($custom)) {
			show_404();
			return;
		}

		$controller = new MY_DBcontroller();
		$controller->_loadCustom($custom->mdl_name, $table_name, $custom->table_prefix);

		return call_user_func_array( array($controller, $method), $arguments);
	}
}
