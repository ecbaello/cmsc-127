<?php

class Database_pcf_model extends MY_DBmodel
{
	protected $TableName = 'pcf';
	protected $TablePrimaryKey = 'pcf_id';

	const MetaTableName = 'pcf_type_table';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		
		$this->createTable();
		$this->createTypeTable();

		$this->registerTypeTable('General');
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function createTypeTable() 
	{
		if (!($this->db->table_exists(self::MetaTableName)))
		{
			$fields = array(
        		'pcf_type' => array(
	                'type' => 'INT',
	                'auto_increment' => TRUE
	            )
       		);
			$this->dbforge->add_field		($fields);
			$this->dbforge->add_field		("pcf_name VARCHAR(100) NOT NULL");
			$this->dbforge->add_key 		('pcf_type', TRUE);
			$this->dbforge->create_table	(self::MetaTableName);
		}
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function createTable()
	{
		if (!($this->db->table_exists($this->TableName)))
		{
			$this->dbforge->add_field		("pcf_type INT NOT NULL");

			$fields = array(
        		$this->TablePrimaryKey => array(
	                'type' => 'INT',
	                'auto_increment' => TRUE
	            )
       		);
			$this->dbforge->add_field		($fields);
			$this->dbforge->add_field		("pcf_particulars VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_supporting_documents VARCHAR(100) DEFAULT ''");
			$this->dbforge->add_field		("pcf_screening_training FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_meals_snacks FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_travel FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_office_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_water FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_communications FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_medical_supplies FLOAT DEFAULT 0.0");
			$this->dbforge->add_field		("pcf_other_expenses FLOAT DEFAULT 0.0");
			$this->dbforge->add_key 		($this->TablePrimaryKey, TRUE);
			$this->dbforge->create_table	($this->TableName);

			$this->registerFieldTitle(  $this->TablePrimaryKey, 'ID');
			$this->registerFieldTitle( 'pcf_particulars', 'Particulars');
			$this->registerFieldTitle( 'pcf_supporting_documents', 'Supporting Documents');
			$this->registerFieldTitle( 'pcf_screening_training', 'Screening/Training');
			$this->registerFieldTitle( 'pcf_meals_snacks', 'Meals/Snacks');
			$this->registerFieldTitle( 'pcf_travel', 'Travel');
			$this->registerFieldTitle( 'pcf_office_supplies', 'Office Supplies');
			$this->registerFieldTitle( 'pcf_water', 'Water');
			$this->registerFieldTitle( 'pcf_communications', 'Communications');
			$this->registerFieldTitle( 'pcf_medical_supplies', 'Medical Supplies');
			$this->registerFieldTitle( 'pcf_other_expenses', 'Other Expenses');
		}
	}

	public function getTypeTable($query) {
		$this->db->select($this->database_model->getFields($this->TableName));
		$this->db->from($this->TableName);
		$this->db->join(self::MetaTableName, $this->TableName.'.pcf_type = '.self::MetaTableName.'.pcf_type');
		$this->db->where('pcf_name', $query);
		$query = $this->db->get();
		return $query;
	}

	public function registerTypeTable($name) {
		if ( !$this->checkExists($name) ) {
			$data = array(
			    'pcf_name' => $name
			);
			$this->db->insert(self::MetaTableName, $data);
		}
		
	}

	public function checkExists($name) {
		$this->db->where('pcf_name', $name);
		$query = $this->db->get(self::MetaTableName);
		return !empty( $query->result_array() );
		
	}

	public function insertIntoTypeTable($name, $values) {
		$values['pcf_type'] = $this->convertNameToType($name);
		$this->insertIntoTable($values);
	}

	public function convertNameToType($name) {
		$this->db->select('pcf_type');
		$this->db->where('pcf_name', $name);
		$query = $this->db->get(self::MetaTableName);

		$query = $query->result_array();
		$query = $query[0];

		return $query['pcf_type'];
	}

}

?>
