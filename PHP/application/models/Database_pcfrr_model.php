<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Database_pcfrr_model extends MY_DBarraymodel
{
	public $ModelTitle = 'Petty Cash Fund Replenishment';
	public $TableName = 'pcf_rr';
	public $TablePrimaryKey = 'pcf_rr_request_id';

	public $categoryTableName = 'pcf_type_table';
	public $arrayFieldName = 'pcf_type';
	public $categoryFieldName = 'pcf_name';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->registerCategoryTable('General');
		$this->registerCategoryTable('Smile Train');
		$this->registerCategoryTable('Cataract');
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function createCategoryTable() 
	{
		if (!($this->db->table_exists($this->categoryTableName)))
		{
			$fields = array(
        		'pcf_type' => array(
	                'type' => 'INT',
	                'auto_increment' => TRUE
	            )
       		);
			$this->dbforge->add_field		($fields);
			$this->dbforge->add_field		("pcf_name VARCHAR(100) NOT NULL");
			$this->dbforge->add_key 		('pcf_type', TRUE);
			$this->dbforge->create_table	($this->categoryTableName);
		}
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
			$this->dbforge->add_field		("pcf_rr_particulars VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_rr_supporting_documents VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_rr_screening_training FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_meals_snacks FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_travel FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_office_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_water FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_communications FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_medical_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_other_expenses FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_other_desc VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_key 		($this->TablePrimaryKey, TRUE);
			$this->dbforge->create_table	($this->TableName);

			$this->registerFieldTitle(  $this->TablePrimaryKey, '#');
			$this->registerFieldTitle( 'pcf_rr_particulars', 'Particulars', 'TEXTAREA');
			$this->registerFieldTitle( 'pcf_rr_supporting_documents', 'Supporting Documents', 'URL');
			$this->registerFieldTitle( 'pcf_rr_screening_training', 'Screening/Training', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_meals_snacks', 'Meals/Snacks', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_travel', 'Travel', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_office_supplies', 'Office Supplies', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_water', 'Water', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_communications', 'Communications', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_medical_supplies', 'Medical Supplies', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_other_expenses', 'Other Expenses', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_other_desc', 'Other Description', 'TEXTAREA');
		}
	}

}

?>
