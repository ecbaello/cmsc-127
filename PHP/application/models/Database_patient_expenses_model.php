<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Database_patient_expenses_model extends MY_DBArchmodel
{
	public $ModelTitle = 'Patient Expenses';
	public $TableName = 'fin_patient_expenses'; // Overideable
	public $TablePrimaryKey = 'pe_transaction_id'; // Overideable
	public $FieldPrefix = 'pe';
	public $dateField = 'pe_date_discharged';

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
			$this->dbforge->add_field		("pe_patient_name varchar(100)");
			$this->dbforge->add_field		("pe_date_of_operation DATE NOT NULL");
			$this->dbforge->add_field		("pe_date_discharged DATE NOT NULL");
			$this->dbforge->add_field		("pe_hospital_bill numeric(12,2) DEFAULT 0.0");
			$this->dbforge->add_field		("pe_medical_supplies numeric(12,2) DEFAULT 0.0");
			$this->dbforge->add_field		("pe_travel numeric(12,2) DEFAULT 0.0");
			$this->dbforge->add_field		("pe_meals numeric(12,2) DEFAULT 0.0");
			$this->dbforge->add_field		("pe_other_expenses numeric(12,2) DEFAULT 0.0");
			$this->dbforge->add_field		("pe_patients_counterpart numeric(12,2) DEFAULT 0.0");
			$this->dbforge->add_field		("pe_op_desc VARCHAR(100) DEFAULT ''");
			
			$this->dbforge->add_key 		($this->TablePrimaryKey, TRUE);
			$this->dbforge->create_table	($this->TableName);

			$this->registerFieldTitle($this->TablePrimaryKey, '#');
			$this->registerFieldTitle('pe_patient_name', 'Patient Name', 'TEXTAREA');
			$this->registerFieldTitle('pe_date_of_operation', 'Date of Operation', 'DATE');
			$this->registerFieldTitle('pe_date_discharged', 'Date Discharged', 'DATE');
			$this->registerFieldTitle('pe_hospital_bill', 'Hospital Bill', 'FLOAT');
			$this->registerFieldTitle('pe_medical_supplies', 'Medical Supplies', 'FLOAT');
			$this->registerFieldTitle('pe_travel', 'Travel', 'FLOAT');
			$this->registerFieldTitle('pe_meals', 'Meals', 'FLOAT');
			$this->registerFieldTitle('pe_other_expenses', 'Other Expenses', 'FLOAT');
			$this->registerFieldTitle('pe_patients_counterpart', 'Patient\'s Counterpart', 'FLOAT');
			$this->registerFieldTitle('pe_op_desc', 'Optional Description', 'TEXTAREA');
			$this->registerModel();
			$this->registerReport();
		}
	}


}

?>