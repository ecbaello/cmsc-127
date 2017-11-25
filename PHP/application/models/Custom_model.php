<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Custom_model extends MY_DBmodel
{
	public $ModelTitle = '';
	public $TableName = ''; // Overideable
	public $TablePrimaryKey = 'id'; // Overideable
	public $FieldPrefix = '';

	public function loadCustom($ModelTitle, $TableName, $FieldPrefix)
	{
		$this->ModelTitle = $ModelTitle;
		$this->TableName = $TableName;
		$this->FieldPrefix = $FieldPrefix;

		$this->init();
	}
}

?>