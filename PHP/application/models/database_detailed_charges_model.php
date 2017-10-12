<?php

class Database_detailed_charges_model extends CI_Model
{
	const TableName = 'detailed_charges';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->createTable();
	}

	public function createTable()
	{
		if (!($this->db->table_exists(self::TableName)))
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
			$this->dbforge->create_table	(self::TableName);

			$this->load->model('database_model');

			$this->database_model->registerFieldTitle(self::TableName, 'dc_charge_id', 'Charge ID');
			$this->database_model->registerFieldTitle(self::TableName, 'dc_date', 'Date');
			$this->database_model->registerFieldTitle(self::TableName, 'dc_description', 'Description');
			$this->database_model->registerFieldTitle(self::TableName, 'dc_quantity', 'Quantity');
			$this->database_model->registerFieldTitle(self::TableName, 'dc_amount', 'Amount');

		}
	}

	public function getTable() {
		return $this->db->get(self::TableName);
	}

	public function insertIntoTable($data) {
		$this->db->insert(self::TableName, $data);
	}

	public function getFieldAssociations() {
		$arr = $this->database_model->getFieldAssociations(self::TableName);
		unset($arr['dc_charge_id']);
		return $arr;
	}

	public function getFields() {
		return $this->database_model->getFields(self::TableName);
	}

	public function deleteWithPK($id) {
		$this->db->where('dc_charge_id', $id);
	    $this->db->delete(self::TableName); 
	}

	public function updateWithPK($id, $data) {
		$this->db->where('dc_charge_id', $id);
	    $this->db->update(self::TableName, $data); 
	}

}

?>