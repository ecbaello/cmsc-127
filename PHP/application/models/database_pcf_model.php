<?php

class Database_pcf_model extends CI_Model
{
	const PCFTableName = 'pcf';
	const PCF_MetaTableName = 'pcf_type_table';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->makePCFTypeTable();
		$this->makePCFTable();
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function makePCFTypeTable() 
	{
		if (!($this->db->table_exists(self::PCF_MetaTableName)))
		{
			$this->dbforge->add_field		("pcf_id INT NOT NULL AUTO_INCREMENT");
			$this->dbforge->add_field		("pcf_name VARCHAR(100) NOT NULL");
			$this->dbforge->add_key 		('pcf_id', TRUE);
			$this->dbforge->add_key 		('pcf_name', TRUE);
			$this->dbforge->create_table	(self::PCF_MetaTableName);
		}
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function makePCFTable()
	{
		if (!($this->db->table_exists(self::PCFTableName)))
		{
			$this->dbforge->add_field		("pcf_id INT NOT NULL");
			$this->dbforge->add_field		("pcf_name VARCHAR(100) NOT NULL DEFAULT ''");

			$this->dbforge->add_field		("pcf_particulars VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_supporting_documents VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_screening_training FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_meals_snacks FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_travel FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_office_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_communications FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_medical_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_other_expenses FLOAT DEFAULT 0.0");
			
			$this->dbforge->create_table	(self::PCFTableName);
		}
	}

	public function getTypeTable($query) {
		$this->db->select('*');
		$this->db->from(self::PCFTableName);
		$this->db->join(self::PCF_MetaTableName, self::PCFTableName+'.id = '+self::PCF_MetaTableName+'.id');
		$this->db->where('pcf_name', $query);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function registerTypeTable($name) {
		$data = array(
		    'pcf_name' => $name
		);

		$this->db->insert(self::PCF_MetaTableName, $data);
	}

	public function insertIntoTypeTable($name, $data) {
		$this->db->get(self::PCF_MetaTableName);
		$this->db->where('pcf_name', $name);

		$query = $this->db->result_array();
		$query = $query[0];

		$data['pcf_id'] = $query;

		$this->db->insert(self::PCF_MetaTableName, $data);
	}
}

?>