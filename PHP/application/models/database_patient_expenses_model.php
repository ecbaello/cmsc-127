<?php

class Database_patient_expenses_model extends MY_DBmodel
{
	protected $TableName = 'patient_expenses'; // Overideable
	protected $TablePrimaryKey = 'pe_transaction_id'; // Overideable

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
		if (!($this->db->table_exists($this->TableName)))
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
			$this->dbforge->create_table	($this->TableName);

			$this->load->model('database_model');

			$this->database_model->registerFieldTitle('pe_transaction_id', 'Transaction ID');
			$this->database_model->registerFieldTitle('pe_hospital_bill', 'Hospital Bill');
			$this->database_model->registerFieldTitle('pe_laboratory', 'Laboratory');
			$this->database_model->registerFieldTitle('pe_medical_supplies', 'Medical Supplies');
			$this->database_model->registerFieldTitle('pe_travel', 'Travel');
			$this->database_model->registerFieldTitle('pe_meals', 'Meals');
			$this->database_model->registerFieldTitle('pe_other_expenses', 'Other Expenses');
			$this->database_model->registerFieldTitle('pe_patients_counterpart', 'Patient\'s Counterpart');
			$this->database_model->registerFieldTitle('pe_op_desc', 'Optional Description');
		}
	}


}

?>