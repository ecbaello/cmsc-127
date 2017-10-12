<?php

class Input_types_model extends CI_Model
{
	const TableName = 'input_types';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->load->library('db_table');

		$this->createTable();
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function createTable() {
		if (!($this->db->table_exists(self::TableName)))
		{
			$this->dbforge->add_field		("table_name VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field_title VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("table_field_inputs BOOLEAN NOT NULL DEFAULT TRUE");
			$this->dbforge->add_field		("table_field_input_type SMALLINT NOT NULL DEFAULT 0");

			$this->dbforge->create_table	(self::TableName);
		}
	}

	public function registerFieldInput($input_name) {
		$this->db->insert(self::TableName, $input_name);
	}
}

?>