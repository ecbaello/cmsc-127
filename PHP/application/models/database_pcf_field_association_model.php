<?php

class Database_pcf_field_association_model extends CI_Model
{
	const PCFFATableName = 'pcf_field_association';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();
		$this->load->model('database_pcf_model');

		$this->makePCFFATable();
		$this->resetDefaultAssociations();
	}

	public function makePCFFATable()
	{
		if (!($this->db->table_exists(self::PCFFATableName)))
		{
			$this->dbforge->add_field		("pcf_id int not null");
			$this->dbforge->add_field		("field varchar(100) not null default ''");
			$this->dbforge->create_table	(self::PCFFATableName);

		}
	}
	
	public function resetDefaultAssociations(){
		
		$query = "truncate ".self::PCFFATableName;
		$this->db->query($query);
		
		$query = "insert into ".self::PCFFATableName." values
			('1','pcf_particulars'),
			('1','pcf_supporting_documents'),
			('1','pcf_screening_training'),
			('1','pcf_meals_snacks'),
			('1','pcf_travel'),
			('1','pcf_office_supplies'),
			('1','pcf_water'),
			('1','pcf_communications'),
			('1','pcf_others'),
			
			('2','pcf_particulars'),
			('2','pcf_supporting_documents'),
			('2','pcf_meals_snacks'),
			('2','pcf_travel'),
			('2','pcf_medical_supplies'),
			('2','pcf_other_expenses'),
			
			('3','pcf_particulars'),
			('3','pcf_supporting_documents'),
			('3','pcf_meals_snacks'),
			('3','pcf_travel'),
			('3','pcf_medical_supplies'),
			('3','pcf_other_expenses')
		";
		
		
		$this->db->query($query);

	}

	public function getTable() {
		return $this->db->get(self::PCFFATableName);
	}

	public function insertIntoTable($data) {
		$this->db->insert(self::PCFFATableName, $data);
	}

}

?>