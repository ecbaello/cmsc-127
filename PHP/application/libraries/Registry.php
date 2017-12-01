<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Registry
{
	public function __construct()
	{
		$this->load->model('Registry_model');
		$this->load->model('Permission_model');
	}
	public function __call($method, $arguments)
	{	
		return call_user_func_array( array($this->Registry_model, $method), $arguments);
	}

	public function __get($var)
	{
		return get_instance()->$var;
	}

	public function unregisterTable($tablename) {
		if (empty($tablename)) return false;

		if (!($this->db->table_exists($tablename))) return false;

		$reg = $this->Registry_model;

		$this->db->where('imported', 1);
		$this->db->where('table_name', $tablename);
		$success = $this->db->delete($reg::modelTableName);
		
		if ($success) {
			$this->db->where('table_name', $tablename);
			$this->db->delete(MY_DBmodel::metaTableName);

			$permiss = $this->Permission_model;

			$this->db->where('table_name', $tablename);
			$this->db->delete($permiss::tableName);
		}
	}

	public function registerTable($tablename, $title) {
		if (empty($tablename)) return false;

		if (!($this->db->table_exists($tablename))) return false;

		if (empty($title)) $title = $tablename;

		// this is database dependent
		$fields = $this->db->field_data($tablename);

		$this->load->model('custom_model');
		$model = $this->custom_model;

		$model->loadCustom($title, $tablename, '');
		$pk = null;

		foreach ($fields as $field) {
			if ($field->primary_key == 1) $pk = $field->name;
		}

		if ($pk == null) return false;
		$model->TablePrimaryKey = $pk;

		foreach ($fields as $field) {
			$name = $field->name;
			$label = ($field->name == $pk)?'#':$name;
			$type = 'TEXT';

			switch (strtoupper($field->type)) {
				case 'DECIMAL':
				case 'FLOAT':
				case 'DOUBLE':
					$type = 'NUMERIC';
					break;
				case 'INT':
					$type = 'NUMBER';
					break;
				case 'DATE':
					$type = 'DATE';
					break;
			}

			$model->registerFieldTitle($name, $label, $type);
		}

		return $this->registerModel(
			$title,
			null,
			0,
			$tablename,
			$pk,
			'',
			0, null, null, null, 1
		);
	}
}