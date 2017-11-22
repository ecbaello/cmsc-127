<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Database_pcf_general_model extends MY_DBpcfmodel
{
	public $ModelTitle = 'Petty Cash Fund: General';
	public $TableName = 'pcf_general';
	public $TablePrimaryKey = 'pcf_expense_id';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class
		$this->init();
	}


	/**
	* Make PCF table if does not exists
	*
	*/
	public function createTable()
	{
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
			$this->dbforge->add_field		("pcf_particulars VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_supporting_documents VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_screening_training FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_meals_snacks FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_travel FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_office_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_water FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_communications FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_other_expenses FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		($this->booleanFieldName." BOOLEAN DEFAULT 0");
			$this->dbforge->add_key 		($this->TablePrimaryKey, TRUE);
			$this->dbforge->create_table	($this->TableName);

			$this->registerFieldTitle(  $this->TablePrimaryKey, '#');
            $this->registerFieldTitle( 'pcf_date', 'Date', 'DATE');
			$this->registerFieldTitle( 'pcf_particulars', 'Particulars', 'TEXTAREA');
			$this->registerFieldTitle( 'pcf_supporting_documents', 'Documents', 'TEXT');
			$this->registerFieldTitle( 'pcf_screening_training', 'Screening/Training', 'FLOAT');
			$this->registerFieldTitle( 'pcf_meals_snacks', 'Meals/Snacks', 'FLOAT');
			$this->registerFieldTitle( 'pcf_travel', 'Travel', 'FLOAT');
			$this->registerFieldTitle( 'pcf_office_supplies', 'Office Supplies', 'FLOAT');
			$this->registerFieldTitle( 'pcf_water', 'Water', 'FLOAT');
			$this->registerFieldTitle( 'pcf_communications', 'Communications', 'FLOAT');
			$this->registerFieldTitle( 'pcf_other_expenses', 'Other Expenses', 'FLOAT');
		}
	}

}

?>