<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Custom_array_model extends MY_DBarraymodel
{
	public $ModelTitle = '';
	public $TableName = ''; // Overideable
	public $TablePrimaryKey = 'id'; // Overideable
	public $FieldPrefix = '';

	public $categoryTableName = '';
	public $arrayFieldName = '';
	public $categoryFieldName = '';

	protected $willRegister = FALSE;

	public function loadCustom($ModelTitle, $TableName, $FieldPrefix, $categoryTableName, $arrayFieldName, $categoryFieldName)
	{
		$this->ModelTitle = $ModelTitle;
		$this->TableName = $TableName;
		$this->FieldPrefix = $FieldPrefix;

		$this->categoryTableName = $categoryTableName;
		$this->arrayFieldName = $arrayFieldName;
		$this->categoryFieldName = $categoryFieldName;

		return $this->db->table_exists($this->TableName);
	}

	protected function getModelClass() {
		return null;
	}
}

?>