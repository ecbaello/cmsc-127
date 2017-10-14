<?php

class Database_detailed_charges_model extends MY_DBmodel
{
	protected $TableName = 'detailed_charges'; // Overideable
	protected $TablePrimaryKey = 'dc_charge_id'; // Overideable

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->createTable();
	}

	public function createTable()
	{
		if (!($this->db->table_exists($this->TableName)))
		{
			$fields = array(
        		'dc_charge_id' => array(
	                'type' => 'INT',
	                'auto_increment' => TRUE
	            )
       		);
			$this->dbforge->add_field		($fields);
			$this->dbforge->add_field		("dc_date date not null");
			$this->dbforge->add_field		("dc_description varchar(100) DEFAULT ''");
			$this->dbforge->add_field		("dc_quantity int DEFAULT 0");
			$this->dbforge->add_field		("dc_amount float DEFAULT 0.0");
			
			$this->dbforge->add_key 		('dc_charge_id', TRUE);
			$this->dbforge->create_table	($this->TableName);

			$this->load->model('database_model');

			$this->database_model->registerFieldTitle('dc_charge_id', 'Charge ID');
			$this->database_model->registerFieldTitle('dc_date', 'Date');
			$this->database_model->registerFieldTitle('dc_description', 'Description');
			$this->database_model->registerFieldTitle('dc_quantity', 'Quantity');
			$this->database_model->registerFieldTitle('dc_amount', 'Amount');

		}
	}

}

?>