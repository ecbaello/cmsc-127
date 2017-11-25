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

		if ($table_name != null) {
			if (isset($arguments[1])) $method = $arguments[1];
			else $method = 'index';
			unset($arguments[1]);

			defined('NAV_SELECT') or define('NAV_SELECT', 1);
			$custom = $this->registry_model->customtable($table_name);

			if (!empty($custom)) {
				$custom = $custom->row();
				$model = new MY_DBmodel;
				$model->ModelTitle = $custom->mdl_name;
				$model->TableName = $table_name;
				$model->FieldPrefix = $custom->table_prefix; // validate not empty
				$model->init();

				$controller = new MY_DBcontroller($model);

				return call_user_func_array( array($controller, $method), $arguments);
			} else show_404();
		}
	}
}
