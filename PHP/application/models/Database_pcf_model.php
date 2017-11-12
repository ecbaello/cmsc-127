<?php

class Database_pcf_model extends MY_DBarraymodel
{
	public $ModelTitle = 'Petty Cash Fund';
	public $TableName = 'pcf';
	public $TablePrimaryKey = 'pcf_expense_id';

	public $categoryTableName = 'pcf_type_table';
	public $arrayFieldName = 'pcf_type';
	public $categoryFieldName = 'pcf_name';

	public $fieldAssociationsTableName = 'pcf_field_associations';

	/**
	* The constructor method
	*
	*/
	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->registerCategoryTable('General');
		$this->registerCategoryTable('Smile Train');
		$this->registerCategoryTable('Cataract');

		$this->createPCFFATable();
	}

	/**
	* Make PCF table if does not exists
	*
	*/
	public function createPCFFATable(){
        if (!($this->db->table_exists($this->fieldAssociationsTableName)))
        {
            $this->dbforge->add_field		("pcf_name varchar(30) not null default 'General'");
            $this->dbforge->add_field		("field varchar(100) not null default ''");
            $this->dbforge->create_table	($this->fieldAssociationsTableName);
            $this->resetDefaultAssociations();
        }
	}

	public function createCategoryTable() 
	{
		if (!($this->db->table_exists($this->categoryTableName)))
		{
			$fields = array(
        		'pcf_type' => array(
	                'type' => 'INT',
	                'auto_increment' => TRUE
	            )
       		);
			$this->dbforge->add_field		($fields);
			$this->dbforge->add_field		("pcf_name VARCHAR(100) NOT NULL");
			$this->dbforge->add_field		("pcf_allotted_fund FLOAT NOT NULL DEFAULT 5000.0");
			$this->dbforge->add_field		("pcf_expense_threshold FLOAT NOT NULL DEFAULT 3000.0");
			$this->dbforge->add_key 		('pcf_type', TRUE);
			$this->dbforge->create_table	($this->categoryTableName);
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
			$this->dbforge->add_field		("pcf_date DATE NOT NULL");
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

			$this->registerFieldTitle(  $this->TablePrimaryKey, '#');
            $this->registerFieldTitle( 'pcf_date', 'Date', 'DATE');
			$this->registerFieldTitle( 'pcf_particulars', 'Particulars', 'TEXTAREA');
			$this->registerFieldTitle( 'pcf_supporting_documents', 'Documents', 'URL');
			$this->registerFieldTitle( 'pcf_screening_training', 'Screening/Training', 'FLOAT');
			$this->registerFieldTitle( 'pcf_meals_snacks', 'Meals/Snacks', 'FLOAT');
			$this->registerFieldTitle( 'pcf_travel', 'Travel', 'FLOAT');
			$this->registerFieldTitle( 'pcf_office_supplies', 'Office Supplies', 'FLOAT');
			$this->registerFieldTitle( 'pcf_water', 'Water', 'FLOAT');
			$this->registerFieldTitle( 'pcf_communications', 'Communications', 'FLOAT');
			$this->registerFieldTitle( 'pcf_medical_supplies', 'Medical Supplies', 'FLOAT');
			$this->registerFieldTitle( 'pcf_other_expenses', 'Other Expenses', 'FLOAT');
		}
	}

    public function resetDefaultAssociations(){

        $query = "truncate ".$this->fieldAssociationsTableName;
        $this->db->query($query);

        $query = "insert into ".$this->fieldAssociationsTableName." values
			('General','pcf_expense_id'),
			('General','pcf_type'),
			('General','pcf_date'),
			('General','pcf_particulars'),
			('General','pcf_supporting_documents'),
			('General','pcf_screening_training'),
			('General','pcf_meals_snacks'),
			('General','pcf_travel'),
			('General','pcf_office_supplies'),
			('General','pcf_water'),
			('General','pcf_communications'),
			('General','pcf_other_expenses'),
			
			('Smile Train','pcf_expense_id'),
			('Smile Train','pcf_type'),
			('Smile Train','pcf_date'),
			('Smile Train','pcf_particulars'),
			('Smile Train','pcf_supporting_documents'),
			('Smile Train','pcf_meals_snacks'),
			('Smile Train','pcf_travel'),
			('Smile Train','pcf_medical_supplies'),
			('Smile Train','pcf_other_expenses'),
			
			('Cataract','pcf_expense_id'),
			('Cataract','pcf_type'),
			('Cataract','pcf_date'),
			('Cataract','pcf_particulars'),
			('Cataract','pcf_supporting_documents'),
			('Cataract','pcf_meals_snacks'),
			('Cataract','pcf_travel'),
			('Cataract','pcf_medical_supplies'),
			('Cataract','pcf_other_expenses')
		";


        $this->db->query($query);

    }

    public function insertAssociation($pcfName,$fieldName){

        $this->db->from($this->fieldAssociationsTableName);
        $this->db->where('pcf_name',$pcfName);
        $this->db->where('field',$fieldName);

        if($this->db->get()->num_rows() == 0){
            $data = array(
                'field'=>$fieldName,
                'pcf_name'=>$pcfName
            );
            $this->db->insert($this->TableName,$data);
        }

    }

    public function removeAssociation($pcfName,$fieldName){

        $this->db->where('pcf_name',$pcfName);
        $this->db->where('field',$fieldName);

        $this->db->delete($this->fieldAssociationsTableName);

    }

    public function getAssociatedFields($subtable){
        $this->db->select('field');
        $this->db->from($this->fieldAssociationsTableName);
        $this->db->where('pcf_name',$subtable);
        try {
            $fields = $this->db->get()->result_array();
        }catch(Exception $e){
            return;
        }
		$data = array();
		foreach($fields as $field) {
			array_push($data, $field['field']);
		}
		return $data;
	}

	public function hideFields($subtable){
        $this->db->select('field');
        $this->db->from($this->fieldAssociationsTableName);
        $this->db->where('pcf_name !=',$subtable);
        try {
            $fields = $this->db->get()->result_array();
        }catch(Exception $e){
            return;
        }
        $data = array();
        foreach($fields as $field) {
            array_push($data, $field['field']);
        }

		$this->fieldsToHide = array_diff($data,$this->getAssociatedFields($subtable));
	}

	/*public function insertField($title, $kind, $default = null, $subtable=null){
		parent::insertField($title,$kind,$default);

        $field = str_replace(' ', '_', $title);
        $field = strtolower(
            ($this->FieldPrefix!=null?$this->FieldPrefix:$this->TableName)
            .'_'.$field);

		if($subtable!==null)
			$this->insertAssociation($subtable,$field);
	}
    public function removeField($field,$subtable=null){
        parent::removeField($field);

        if($subtable!==null)
        	$this->removeAssociation($subtable,$field);
    }*/

}

?>
