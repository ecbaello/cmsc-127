<?php

class Database_pcfrr_model extends CI_Model
{
	const PCF_RRTableName = 'pcf_replenishment_request';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();

		$this->makePCFRepReqTable();
	}

	/**
	* Make PCF Replenishment Request table if does not exists
	*
	*/
	public function makePCFRepReqTable()
	{
		if (!($this->db->table_exists(self::PCF_RRTableName)))
		{
			$this->dbforge->add_field		("pcf_rr_request_id INT NOT NULL");
			$this->dbforge->add_field		("pcf_rr_date DATE NOT NULL");

			$this->dbforge->add_field		("pcf_rr_pcf_id INT NOT NULL");

			$this->dbforge->add_field		("pcf_rr_particulars VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_rr_supporting_documents VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_rr_screening_training FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_meals_snacks FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_travel FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_office_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_communications FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_medical_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_other_expenses FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_rr_op_desc VARCHAR(100) DEFAULT ''");
			
			$this->dbforge->create_table	(self::PCF_RRTableName);

			$this->load->model('database_model');

			$this->database_model->registerFieldTitle(self::PCF_RRTableName, 'pcf_rr_request_id', 'Request ID');
			$this->database_model->registerFieldTitle(self::PCF_RRTableName, 'pcf_rr_date', 'Request Date');
			$this->database_model->registerFieldTitle(self::PCF_RRTableName, 'pcf_rr_particulars', 'Particulars');
			$this->database_model->registerFieldTitle(self::PCF_RRTableName, 'pcf_rr_supporting_documents', 'Supporting Documents');
			$this->database_model->registerFieldTitle(self::PCF_RRTableName, 'pcf_rr_screening_training', 'Screening/Training');
			$this->database_model->registerFieldTitle(self::PCF_RRTableName, 'pcf_rr_meals_snacks', 'Meals/Snacks');
			$this->database_model->registerFieldTitle(self::PCF_RRTableName, 'pcf_rr_travel', 'Travel');
			$this->database_model->registerFieldTitle(self::PCF_RRTableName, 'pcf_rr_office_supplies', 'Office Supplies');
			$this->database_model->registerFieldTitle(self::PCF_RRTableName, 'pcf_rr_communications', 'Communications');
			$this->database_model->registerFieldTitle(self::PCF_RRTableName, 'pcf_rr_medical_supplies', 'Medical Supplies');
			$this->database_model->registerFieldTitle(self::PCF_RRTableName, 'pcf_rr_other_expenses', 'Other Expenses');
			$this->database_model->registerFieldTitle(self::PCF_RRTableName, 'pcf_rr_op_desc', 'Other Description');

		}
	}

}

?>