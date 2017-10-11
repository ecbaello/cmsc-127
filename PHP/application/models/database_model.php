<?php

class Database_model extends CI_Model
{

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->makePCFMeta();
		$this->makePCFTable();
		$this->makePCFRepReqTable();

	}
	
	public function test_main()
	{
		

		$var = $this->db->query('');

		

	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function makePCFMeta() {
		if (!($this->db->table_exists('pcf_meta')))
		{
			$this->dbforge->add_field		("pcf_id INT NOT NULL");
			$this->dbforge->add_field		("pcf_name VARCHAR(100) DEFAULT ''");

			$this->dbforge->create_table	('pcf_meta');
		}
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function makePCFTable()
	{
		if (!($this->db->table_exists('pcf')))
		{
			$this->dbforge->add_field		("pcf_id SMALLINT NOT NULL");
			$this->dbforge->add_field		("pcf_type VARCHAR(100) NOT NULL DEFAULT ''");

			$this->dbforge->add_field		("pcf_particulars VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_supporting_documents VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_screening_training FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_meals_snacks FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_travel FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_office_supplies FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_communications FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_medical_supplies FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_other_expenses FLOAT NOT NULL DEFAULT 0.0");
			
			$this->dbforge->create_table	('pcf');
		}
	}

	/**
	* Make PCF Replenishment Request table if does not exists
	*
	*/
	public function makePCFRepReqTable()
	{
		if (!($this->db->table_exists('pcf_replenishment_request')))
		{
			$this->dbforge->add_field		("pcf_rr_request_id INT NOT NULL");
			$this->dbforge->add_field		("pcf_rr_date DATE NOT NULL");

			$this->dbforge->add_field		("pcf_rr_pcf_id INT NOT NULL");

			$this->dbforge->add_field		("pcf_rr_particulars VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_rr_supporting_documents VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_rr_screening_training FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_meals_snacks FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_travel FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_office_supplies FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_communications FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_medical_supplies FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_other_expenses FLOAT NOT NULL DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_op_desc VARCHAR(100) DEFAULT ''");
			
			$this->dbforge->create_table	('pcf_replenishment_request');
		}
	}
}

?>
