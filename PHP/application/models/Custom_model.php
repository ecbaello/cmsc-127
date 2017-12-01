<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Custom_model extends MY_DBmodel
{
	public $ModelTitle = '';
	public $TableName = ''; // Overideable
	public $TablePrimaryKey = 'id'; // Overideable
	public $FieldPrefix = '';

	protected $willRegister = FALSE;

	public function loadCustom($ModelTitle, $TableName, $FieldPrefix, $pk = null)
	{
		$this->ModelTitle = $ModelTitle;
		$this->TableName = $TableName;
		$this->FieldPrefix = $FieldPrefix;
		if (!empty($pk)) $this->TablePrimaryKey = $pk;

		return $this->db->table_exists($this->TableName);
	}

	protected function getModelClass() {
		return null;
	}
}

?>