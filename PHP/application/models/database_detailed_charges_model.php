<?php

class Database_detailed_charges_model extends MY_DBmodel
{
	protected $TableName = 'detailed_charges'; // Overideable
	protected $TablePrimaryKey = 'dc_charge_id'; // Overideable

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
			$this->dbforge->add_field		("dc_date date not null");
			$this->dbforge->add_field		("dc_description varchar(100) DEFAULT ''");
			$this->dbforge->add_field		("dc_quantity int DEFAULT 0");
			$this->dbforge->add_field		("dc_amount float DEFAULT 0.0");
			
			$this->dbforge->add_key 		($this->TablePrimaryKey, TRUE);
			$this->dbforge->create_table	($this->TableName);

			$this->load->model('database_model');

			$this->registerFieldTitle( $this->TablePrimaryKey, 'Charge ID');
			$this->registerFieldTitle('dc_date', 'Date');
			$this->registerFieldTitle('dc_description', 'Description');
			$this->registerFieldTitle('dc_quantity', 'Quantity');
			$this->registerFieldTitle('dc_amount', 'Amount');

		}
	}

}

?>