<?php

class Database_patient_expenses_model extends MY_DBmodel
{
	public $ModelTitle = 'Patient Expenses';
	protected $TableName = 'patient_expenses'; // Overideable
	protected $TablePrimaryKey = 'pe_transaction_id'; // Overideable


	public function createTable()
	{
		if (!($this->db->table_exists($this->TableName)))
		{
			$fields = array(
        		$this->TablePrimaryKey => array(
	                'type' => 'INT',
	                'constraint' => 9,
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
			
			$this->dbforge->add_key 		($this->TablePrimaryKey, TRUE);
			$this->dbforge->create_table	($this->TableName);

			$this->registerFieldTitle($this->TablePrimaryKey, 'Transaction ID');
			$this->registerFieldTitle('pe_hospital_bill', 'Hospital Bill');
			$this->registerFieldTitle('pe_laboratory', 'Laboratory');
			$this->registerFieldTitle('pe_medical_supplies', 'Medical Supplies');
			$this->registerFieldTitle('pe_travel', 'Travel');
			$this->registerFieldTitle('pe_meals', 'Meals');
			$this->registerFieldTitle('pe_other_expenses', 'Other Expenses');
			$this->registerFieldTitle('pe_patients_counterpart', 'Patient\'s Counterpart');
			$this->registerFieldTitle('pe_op_desc', 'Optional Description');
		}
	}


}

?>