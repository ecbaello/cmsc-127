<?php

class Database_pcf_model extends MY_DBarraymodel
{
	public $ModelTitle = 'Petty Cash Fund';
	public $TableName = 'pcf';
	public $TablePrimaryKey = 'pcf_id';

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
			$this->dbforge->add_field		("pcf_particulars VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_supporting_documents VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_screening_training FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_meals_snacks FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_travel FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_office_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_water FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_communications FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_medical_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_other_expenses FLOAT DEFAULT 0.0");
			$this->dbforge->add_key 		($this->TablePrimaryKey, TRUE);
			$this->dbforge->create_table	($this->TableName);

			$this->registerFieldTitle(  $this->TablePrimaryKey, 'ID');
			$this->registerFieldTitle( 'pcf_particulars', 'Particulars', 'TEXTAREA');
			$this->registerFieldTitle( 'pcf_supporting_documents', 'Supporting Documents', 'URL');
			$this->registerFieldTitle( 'pcf_screening_training', 'Screening/Training', 'FLOAT');
			$this->registerFieldTitle( 'pcf_meals_snacks', 'Meals/Snacks', 'FLOAT');
			$this->registerFieldTitle( 'pcf_travel', 'Travel', 'FLOAT');
			$this->registerFieldTitle( 'pcf_office_supplies', 'Office Supplies', 'FLOAT');
			$this->registerFieldTitle( 'pcf_water', 'Water', 'FLOAT');
			$this->registerFieldTitle( 'pcf_communications', 'Communications', 'FLOAT');
			$this->registerFieldTitle( 'pcf_medical_supplies', 'Medical Supplies', 'FLOAT');
			$this->registerFieldTitle( 'pcf_other_expenses', 'Other Expenses', 'FLOAT');
		}
	}

}

?>
