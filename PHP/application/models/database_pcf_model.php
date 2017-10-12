<?php

class Database_pcf_model extends CI_Model
{
	const TableName = 'pcf';
	const MetaTableName = 'pcf_type_table';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->load->model('database_model');
		
		$this->createTable();
		$this->makePCFTypeTable();

		$this->registerTypeTable('General');
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function makePCFTypeTable() 
	{
		if (!($this->db->table_exists(self::MetaTableName)))
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
			$this->dbforge->create_table	(self::MetaTableName);
		}
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function createTable()
	{
		if (!($this->db->table_exists(self::TableName)))
		{
			$this->dbforge->add_field		("pcf_type INT NOT NULL");

			$fields = array(
        		'pcf_id' => array(
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
			$this->dbforge->add_key 		('pcf_id', TRUE);
			$this->dbforge->create_table	(self::TableName);

			$this->load->model('database_model');

			$this->database_model->registerFieldTitle(self::TableName, 'pcf_id', 'ID');
			$this->database_model->registerFieldTitle(self::TableName, 'pcf_particulars', 'Particulars');
			$this->database_model->registerFieldTitle(self::TableName, 'pcf_supporting_documents', 'Supporting Documents');
			$this->database_model->registerFieldTitle(self::TableName, 'pcf_screening_training', 'Screening/Training');
			$this->database_model->registerFieldTitle(self::TableName, 'pcf_meals_snacks', 'Meals/Snacks');
			$this->database_model->registerFieldTitle(self::TableName, 'pcf_travel', 'Travel');
			$this->database_model->registerFieldTitle(self::TableName, 'pcf_office_supplies', 'Office Supplies');
			$this->database_model->registerFieldTitle(self::TableName, 'pcf_water', 'Water');
			$this->database_model->registerFieldTitle(self::TableName, 'pcf_communications', 'Communications');
			$this->database_model->registerFieldTitle(self::TableName, 'pcf_medical_supplies', 'Medical Supplies');
			$this->database_model->registerFieldTitle(self::TableName, 'pcf_other_expenses', 'Other Expenses');
		}
	}

	public function getTypeTable($query) {
		$this->db->select($this->database_model->getFields(self::TableName));
		$this->db->from(self::TableName);
		$this->db->join(self::MetaTableName, self::TableName.'.pcf_type = '.self::MetaTableName.'.pcf_type');
		$this->db->where('pcf_name', $query);
		$query = $this->db->get();
		return $query;
	}

	public function registerTypeTable($name) {
		if ( !$this->checkExists($name) ) {
			$data = array(
			    'pcf_name' => $name
			);
			$this->db->insert(self::MetaTableName, $data);
		}
		
	}

	public function checkExists($name) {
		$this->db->where('pcf_name', $name);
		$query = $this->db->get(self::MetaTableName);
		return !empty( $query->result_array() );
		
	}

	public function insertIntoTypeTable($name, $values) {
		$values['pcf_type'] = convertNameToType('pcf_type');
		$this->db->insert(self::TableName, $values);
	}

	public function convertNameToType($name) {
		$this->db->select('pcf_type');
		$this->db->where('pcf_name', $name);
		$query = $this->db->get(self::MetaTableName);

		$query = $query->result_array();
		$query = $query[0];

		return $query['pcf_type'];
	}

	public function getFieldAssociations() {
		$arr = $this->database_model->getFieldAssociations(self::TableName);
		unset($arr['pcf_id']);
		return $arr;
	}

	public function getFields() {
		return $this->database_model->getFields(self::TableName);
	}

	public function deleteWithPK($id) {
		$this->db->where('pcf_id', $id);
	    $this->db->delete(self::TableName); 
	}

	public function updateWithPK($id, $data) {
		$this->db->where('pcf_id', $id);
	    $this->db->update(self::TableName, $data); 
	}
}

?>
