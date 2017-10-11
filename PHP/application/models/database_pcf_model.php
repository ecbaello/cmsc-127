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

		
		$this->makePCFTable();
		$this->makePCFTypeTable();

		$this->registerTypeTable('General');
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function makePCFTypeTable() 
	{
		if (!($this->db->table_exists(self::PCF_MetaTableName)))
		{
			$fields = array(
        		'pcf_id' => array(
	                'type' => 'INT',
	                'auto_increment' => TRUE
	            )
       		);
			$this->dbforge->add_field		($fields);
			$this->dbforge->add_field		("pcf_name VARCHAR(100) NOT NULL");
			$this->dbforge->add_key 		('pcf_id', TRUE);
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

			$this->load->model('database_model');

			$this->database_model->registerFieldTitle(self::PCFTableName, 'pcf_particulars', 'Particulars');
			$this->database_model->registerFieldTitle(self::PCFTableName, 'pcf_supporting_documents', 'Supporting Documents');
			$this->database_model->registerFieldTitle(self::PCFTableName, 'pcf_screening_training', 'Screening/Training');
			$this->database_model->registerFieldTitle(self::PCFTableName, 'pcf_meals_snacks', 'Meals/Snacks');
			$this->database_model->registerFieldTitle(self::PCFTableName, 'pcf_travel', 'Travel');
			$this->database_model->registerFieldTitle(self::PCFTableName, 'pcf_office_supplies', 'Office Supplies');
			$this->database_model->registerFieldTitle(self::PCFTableName, 'pcf_communications', 'Communications');
			$this->database_model->registerFieldTitle(self::PCFTableName, 'pcf_medical_supplies', 'Medical Supplies');
			$this->database_model->registerFieldTitle(self::PCFTableName, 'pcf_other_expenses', 'Other Expenses');
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
		$this->db->where('pcf_name', $name);
		$query = $this->db->get(self::PCF_MetaTableName);
		// Check availability
		if ( empty( $query->result_array() ) ) {

			$data = array(
			    'pcf_name' => $name
			);
			$this->db->insert(self::PCF_MetaTableName, $data);
		}
		
	}

	public function insertIntoTypeTable($name, $values) {
		$this->db->select('pcf_id');
		$this->db->where('pcf_name', $name);
		$query = $this->db->get(self::PCF_MetaTableName);

		$query = $query->result_array();
		$query = $query[0];

		$values['pcf_id'] = $query['pcf_id'];
		

		$this->db->insert(self::PCFTableName, $values);
	}
}

?>