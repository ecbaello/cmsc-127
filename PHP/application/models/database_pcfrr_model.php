<?php

class Database_pcfrr_model extends MY_DBmodel
{
	public $ModelTitle = 'Petty Cash Fund Replenishment';
	protected $TableName = 'pcf_replenishment_request'; // Overideable
	protected $TablePrimaryKey = 'pcf_rr_request_id'; // Overideable

	/**
	* Make PCF Replenishment Request table if does not exists
	*
	*/
	public function createTable() {
		if (!($this->db->table_exists($this->TableName))) {
			$fields = array(
        		$this->TablePrimaryKey => array(
	                'type' => 'INT',
	                'constraint' => 9,
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

			$this->dbforge->add_key 		($this->TablePrimaryKey, TRUE);
			$this->dbforge->create_table	($this->TableName);

			$this->registerFieldTitle(	$this->TablePrimaryKey, 'Request ID');
			$this->registerFieldTitle( 'pcf_rr_pcf_type', 'PCF Type');
			$this->registerFieldTitle( 'pcf_rr_date', 'Request Date', 'DATE');
			$this->registerFieldTitle( 'pcf_rr_particulars', 'Particulars', 'TEXTAREA');
			$this->registerFieldTitle( 'pcf_rr_supporting_documents', 'Supporting Documents', 'URL');
			$this->registerFieldTitle( 'pcf_rr_screening_training', 'Screening/Training', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_meals_snacks', 'Meals/Snacks', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_travel', 'Travel', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_office_supplies', 'Office Supplies', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_water', 'Water', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_communications', 'Communications', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_medical_supplies', 'Medical Supplies', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_other_expenses', 'Other Expenses', 'FLOAT');
			$this->registerFieldTitle( 'pcf_rr_op_desc', 'Other Description', 'TEXTAREA');

		}
	}
}

?>
