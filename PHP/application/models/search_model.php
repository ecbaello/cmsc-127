<?php
class Search_model extends MY_DBmodel {
	protected $TableName = '';
	public function loadTable ($name) {
		$this->TableName = $name;
	}
}
?>