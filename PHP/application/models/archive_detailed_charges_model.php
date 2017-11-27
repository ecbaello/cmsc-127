<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class archive_detailed_charges_model extends MY_Archmodel
{
	public $ModelTitle = 'Detailed Charges Archives';
	public $TableName = 'detailed_charges_arch'; // Overideable
	public $TablePrimaryKey = 'dc_charge_id'; // Overideable
	public $FieldPrefix = 'dc';

	public function createTable()
	{
		if (!($this->db->table_exists($this->TableName)))
		{
			$fields = array(
        		$this->TablePrimaryKey => array(
	                'type' => 'INT',
	                'constraint' => 9,
	                'auto_increment' => FALSE
	            )
       		);
			$this->dbforge->add_field		($fields);
			$this->dbforge->add_field		("dc_patient_id int not null");
			$this->dbforge->add_field		("dc_date date not null");
			$this->dbforge->add_field		("dc_description varchar(100) DEFAULT ''");
			$this->dbforge->add_field		("dc_quantity int DEFAULT 0");
			$this->dbforge->add_field		("dc_amount numeric(12,2) DEFAULT 0.0");
			
			$this->dbforge->add_key 		($this->TablePrimaryKey, TRUE);
			$this->dbforge->create_table	($this->TableName);

			$this->registerFieldTitle( $this->TablePrimaryKey, '#');
			$this->registerFieldTitle('dc_patient_id', 'Patient ID', 'NUMBER');
			$this->registerFieldTitle('dc_date', 'Date', 'DATE');
			$this->registerFieldTitle('dc_description', 'Description', 'TEXTAREA');
			$this->registerFieldTitle('dc_quantity', 'Quantity', 'NUMBER');
			$this->registerFieldTitle('dc_amount', 'Amount', 'FLOAT');
			$this->registerModel();
		}
	}
}

?>