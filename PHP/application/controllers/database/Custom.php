<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom extends CI_Controller {

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->model('registry_model');
	}

	public function load($table_name = null, $method = null, $arg = null)
	{
		if ($table_name != null) {
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
				if (empty($method)) {
					$controller->index();
				} else {
					if (empty($arg)) {
						$controller->{$method}();
					} else {
						$controller->{$method}($arg);
					}
				}
			} else show_404();
		}
		
	}

	
}
