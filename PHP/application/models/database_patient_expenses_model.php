<?php

class Database_patient_expenses_model extends CI_Model
{
	const TableName = 'patient_expenses';

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
        		'pe_transaction_id' => array(
	                'type' => 'INT',
	                'auto_increment' => TRUE
	            )
       		);
			$this->dbforge->add_field		($fields);
			$this->dbforge->add_field		("pe_hospital_bill float DEFAULT 0.0");
			$this->dbforge->add_field		("pe_laboratory FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pe_medical_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pe_travel FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pe_meals FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pe_other_expenses FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pe_patients_counterpart FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pe_op_desc VARCHAR(100) DEFAULT ''");
			
			$this->dbforge->add_key 		('pe_transaction_id', TRUE);
			$this->dbforge->create_table	(self::TableName);

			$this->load->model('database_model');

			$this->database_model->registerFieldTitle(self::TableName, 'pe_transaction_id', 'Transaction ID');
			$this->database_model->registerFieldTitle(self::TableName, 'pe_hospital_bill', 'Hospital Bill');
			$this->database_model->registerFieldTitle(self::TableName, 'pe_laboratory', 'Laboratory');
			$this->database_model->registerFieldTitle(self::TableName, 'pe_medical_supplies', 'Medical Supplies');
			$this->database_model->registerFieldTitle(self::TableName, 'pe_travel', 'Travel');
			$this->database_model->registerFieldTitle(self::TableName, 'pe_meals', 'Meals');
			$this->database_model->registerFieldTitle(self::TableName, 'pe_other_expenses', 'Other Expenses');
			$this->database_model->registerFieldTitle(self::TableName, 'pe_patients_counterpart', 'Patient\'s Counterpart');
			$this->database_model->registerFieldTitle(self::TableName, 'pe_op_desc', 'Optional Description');
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
		unset($arr['pe_transaction_id']);
		return $arr;
	}

	public function getFields() {
		return $this->database_model->getFields(self::TableName);
	}

	public function deleteWithPK($id) {
		$this->db->where('pe_transaction_id', $id);
	    $this->db->delete(self::TableName); 
	}

	public function updateWithPK($id, $data) {
		$this->db->where('pe_transaction_id', $id);
	    $this->db->update(self::TableName, $data); 
	}


}

?>