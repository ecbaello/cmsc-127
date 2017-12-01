<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
*/
class Database_ion_model extends CI_Model
{
	// Table names
	private $groups			= 'groups';
	private $users			= 'users';
	private $login_attempts	= 'login_attempts';
	
	// Join names
	private $users_groups	= 'users_groups';

	private $user_id = 'user_id';
	private $group_id = 'group_id';
	
	function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->database();
		$this->load->dbforge();
	}

	public function use_config() 
	{
		/*
		* If you have the parameter set to use the config table and join names
		* this will change them for you
		*/
		$this->config->load('ion_auth', TRUE);
		$tables = $this->config->item('tables', 'ion_auth');
		$joins = $this->config->item('join', 'ion_auth');

		// table names
		$this->groups		= $tables['groups'];
		$this->users		= $tables['users']; 
		$this->login_attempts = $tables['login_attempts'];
		// join names                          
		$this->users_groups = $tables['users_groups'];

		$this->user_id = $joins['users'];
		$this->group_id = $joins['groups'];

	}
	
	public function up() 
	{	
		/*
		* In order to  add default data with migrations 
		*/
		//$this->load->library('database');
		
		// Function to use config variables
		$this->use_config();
		
		// groups
		if (!$this->db->table_exists($this->groups)) 
		{	
			// Setup Keys
			$this->dbforge->add_key('id', TRUE);
			
			$this->dbforge->add_field(array(
				'id' => array('type' => 'MEDIUMINT', 'constraint' => 8, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
				'name' => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => FALSE),
				'description' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE)
			));
			// create table
			$this->dbforge->create_table($this->groups, TRUE);
			
			// default data
			$this->db->insert($this->groups, array('id'=>null,'name'=>'admin','description'=>'Administrator'));
			$this->db->insert($this->groups, array('id'=>null,'name'=>'members','description'=>'General User'));
		}

		// users
		if (!$this->db->table_exists($this->users)) 
		{	
			// Setup Keys
			$this->dbforge->add_key('id', TRUE);
			
			$this->dbforge->add_field(array(
				'id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
				'ip_address' => array('type' => 'VARCHAR', 'constraint' => '45', 'null' => FALSE),
				'username' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE),
				'password' => array('type' => 'VARCHAR', 'constraint' => '255', 'null' => FALSE),
				'salt' => array('type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE),
				'email' => array('type' => 'VARCHAR', 'constraint' => '254', 'null' => FALSE),
				'activation_code' => array('type' => 'VARCHAR', 'constraint' => '40', 'null' => TRUE),
				'forgotten_password_code' => array('type' => 'VARCHAR', 'constraint' => '40', 'null' => TRUE),
				'forgotten_password_time' => array('type' => 'int', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE),
				'remember_code' => array('type' => 'VARCHAR', 'constraint' => '40', 'null' => TRUE),
				'created_on' => array('type' => 'int', 'constraint' => '11', 'unsigned' => TRUE, 'null' => FALSE),
				'last_login' => array('type' => 'int', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE),
				'active' => array('type' => 'tinyint', 'constraint' => '1', 'unsigned' => TRUE, 'null' => TRUE),
				'first_name' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE),
				'last_name' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE),
				'company' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE),
				'phone' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE)
			));
			// create table
			$this->dbforge->create_table($this->users, TRUE);
			
			// default data
			$data = array(
				'ip_address'=> '127.0.0.1',
				'username'=>'administrator',
				'password'=>'$2a$07$SeBknntpZror9uyftVopmu61qg0ms8Qv1yV6FG.kQOSM.9QhmTo36',
				'salt'=>'',
				'email'=>'admin@admin.com',
				'activation_code'=>'',
				'forgotten_password_code'=>NULL,
				'created_on'=>'1268889823',
				'last_login'=>'1268889823',
				'active'=>'1',
				'first_name' => 'Admin',
				'last_name' => 'istrator',
				'company' => 'ADMIN',
				'phone' => '0'
			);
			$this->db->insert($this->users, $data);
		}
		
		// users_groups 
		if (!$this->db->table_exists($this->users_groups)) 
		{
			// Setup keys
			$this->dbforge->add_key('id', TRUE);
			
			// Build Schema 
			$this->dbforge->add_field(array(
				'id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
				$this->user_id => array('type' => 'int', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE),
				$this->group_id => array('type' => 'MEDIUMINT', 'constraint' => 8, 'unsigned' => TRUE, 'null' => FALSE)
			));
			// create table
			$this->dbforge->create_table($this->users_groups, TRUE);
			
			// define default data
			$data = array(
				array(
					$this->user_id  => 1,
					$this->group_id => 1,
				),
				array(
					$this->user_id  => 1,
					$this->group_id => 2
				)
			);
			// Insert data
			$this->db->insert_batch($this->users_groups, $data);
		}
		if (!$this->db->table_exists($this->login_attempts)) 
		{
			// Setup Keys
			$this->dbforge->add_key('id', TRUE);
			
			$this->dbforge->add_field(array(
				'id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
				'ip_address' => array('type' => 'VARCHAR', 'constraint' => '45', 'null' => FALSE),
				'login' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE),
				'time' => array('type' => 'int', 'constraint' => '11', 'unsigned' => TRUE, 'null' => FALSE)
			));
			// create table
			$this->dbforge->create_table($this->login_attempts, TRUE);
		}
	}

	public function down() 
	{
		// Function to use config variables if 
		$this->use_config();
		
		$this->dbforge->drop_table($this->groups);
		$this->dbforge->drop_table($this->users);
		$this->dbforge->drop_table($this->users_groups);
		$this->dbforge->drop_table($this->login_attempts);
	}
}