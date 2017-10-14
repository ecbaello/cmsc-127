<?php

class Database_pcfrr_model extends MY_DBmodel
{
	protected $TableName = 'pcf_replenishment_request'; // Overideable
	protected $TablePrimaryKey = 'pcf_rr_request_id'; // Overideable

	/**
	* The constructor method
	*
	*/
	public function __construct() {
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->createTable();
	}

	/**
	* Make PCF Replenishment Request table if does not exists
	*
	*/
	public function createTable() {
		if (!($this->db->table_exists($this->TableName))) {
			$fields = array(
        		'pcf_rr_request_id' => array(
	                'type' => 'INT',
	                'auto_increment' => TRUE
	            )
       		);
			$this->dbforge->add_field		($fields);
			$this->dbforge->add_field		("pcf_rr_date DATE NOT NULL");

			$this->dbforge->add_field		("pcf_rr_pcf_type INT NOT NULL");

			$this->dbforge->add_field		("pcf_rr_particulars VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_rr_supporting_documents VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_rr_screening_training FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_meals_snacks FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_travel FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_office_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_water FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_communications FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_medical_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_other_expenses FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_op_desc VARCHAR(100) DEFAULT ''");

			$this->dbforge->add_key 		('pcf_rr_request_id', TRUE);
			
			$this->dbforge->create_table	($this->TableName);

			$this->load->model('database_model');
			$this->database_model->registerFieldTitle( 'pcf_rr_pcf_type', 'PCF Type');
			$this->database_model->registerFieldTitle( 'pcf_rr_request_id', 'Request ID');
			$this->database_model->registerFieldTitle( 'pcf_rr_date', 'Request Date');
			$this->database_model->registerFieldTitle( 'pcf_rr_particulars', 'Particulars');
			$this->database_model->registerFieldTitle( 'pcf_rr_supporting_documents', 'Supporting Documents');
			$this->database_model->registerFieldTitle( 'pcf_rr_screening_training', 'Screening/Training');
			$this->database_model->registerFieldTitle( 'pcf_rr_meals_snacks', 'Meals/Snacks');
			$this->database_model->registerFieldTitle( 'pcf_rr_travel', 'Travel');
			$this->database_model->registerFieldTitle( 'pcf_rr_office_supplies', 'Office Supplies');
			$this->database_model->registerFieldTitle( 'pcf_rr_water', 'Water');
			$this->database_model->registerFieldTitle( 'pcf_rr_communications', 'Communications');
			$this->database_model->registerFieldTitle( 'pcf_rr_medical_supplies', 'Medical Supplies');
			$this->database_model->registerFieldTitle( 'pcf_rr_other_expenses', 'Other Expenses');
			$this->database_model->registerFieldTitle( 'pcf_rr_op_desc', 'Other Description');

		}
	}
}

?>
