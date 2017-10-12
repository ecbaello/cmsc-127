<?php

class Database_patient_expenses_model extends CI_Model
{
	const DCTableName = 'detailed_charges';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->makeDCTable();
	}

	public function makeDCTable()
	{
		if (!($this->db->table_exists(self::DCTableName)))
		{
			$this->dbforge->add_field		("dc_date date not null");
			$this->dbforge->add_field		("dc_description varchar(100) DEFAULT ''");
			$this->dbforge->add_field		("dc_quantity int DEFAULT 0");
			$this->dbforge->add_field		("dc_amount float DEFAULT 0.0");
			
			$this->dbforge->add_key 		('dc_charge_id', TRUE);
			$this->dbforge->create_table	(self::DCTableName);

			$this->load->model('database_model');

			$this->database_model->registerFieldTitle(self::DCTableName, 'dc_charge_id', 'Charge ID');
			$this->database_model->registerFieldTitle(self::DCTableName, 'dc_date', 'Date');
			$this->database_model->registerFieldTitle(self::DCTableName, 'dc_description', 'Description');
			$this->database_model->registerFieldTitle(self::DCTableName, 'dc_quantity', 'Quantity');
			$this->database_model->registerFieldTitle(self::DCTableName, 'dc_amount', 'Amount');

		}
	}

	public function getTable() {
		return $this->db->get(self::DCTableName);
	}

	public function insertIntoTable($data) {
		$this->db->insert(self::DCTableName, $data);
	}

}

?>