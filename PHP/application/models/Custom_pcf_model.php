<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Custom_pcf_model extends MY_DBpcfmodel
{
	public $ModelTitle = '';
	public $TableName = ''; // Overideable
	public $TablePrimaryKey = 'pcf_expense_id'; // Overideable

	protected $willRegister = FALSE;

	public function loadCustom($ModelTitle, $TableName)
	{
		$this->ModelTitle = $ModelTitle;
		$this->TableName = $TableName;

		return $this->db->table_exists($this->TableName);
	}

	protected function getModelClass() {
		return null;
	}
	
	public function initializeCustomTable() {
		
		if (!($this->db->table_exists($this->TableName)))
		{
			$this->dbforge->add_field		("pcf_type INT NOT NULL");

			$fields = array(
        		$this->TablePrimaryKey => array(
	                'type' => 'INT',
	                'auto_increment' => TRUE
	            )
       		);
			$this->dbforge->add_field		($fields);
            $this->dbforge->add_field		("pcf_date DATE NOT NULL");
			$this->dbforge->add_field		($this->booleanFieldName." BOOLEAN DEFAULT 0");
			$this->dbforge->add_key 		($this->TablePrimaryKey, TRUE);
			$this->dbforge->create_table	($this->TableName);

			$this->registerFieldTitle(  $this->TablePrimaryKey, '#');
            $this->registerFieldTitle( 'pcf_date', 'Date', 'DATE');
			return $this->registerModel();
		}
		return false;
	}
}

?>