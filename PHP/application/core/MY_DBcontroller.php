<?php

class MY_DBcontroller extends CI_Controller
{

	protected $model = NULL;

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->library('session');
		$this->load->helper('url');

		define('NAV_SELECT', 1);
	}

	public function index() {
		$this->makeHTML();
	}

	protected function getAccessURL($file_url) {
		return preg_replace('/\\.[^.\\s]{3,4}$/', '', str_replace(APPPATH.'controllers/', '', $file_url));
	}

	protected function makeHTML() {
		$this->load->view('header');

		$this->makeTableHTML();
		
		$this->load->view('footer');
	}

	public function makeTableHTML()
	{
		$this->load->view('table_view', ['title' => $this->model->ModelTitle]);
	}


	public function get ($id) {
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
    	echo json_encode( 
    	[
    		'data'=>$this->model->getByPK($id),
    		'csrf' => $token,
			'csrf_hash' => $hash
		], JSON_NUMERIC_CHECK);
	}

	public function search () {
		$query = json_decode($this->input->post('data'), true);

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();

		$qry = $this->model->find($query);
		$db = empty($qry)? false :$qry->result();
		
		echo json_encode( 
			array(
				'id'=>$this->model->TablePrimaryKey,
				'headers'=>$this->model->getFieldAssociations(),
				'data'=> $db,
				'csrf' => $token,
				'csrf_hash' => $hash,
				'count' => $qry->num_rows()
			)

		, JSON_NUMERIC_CHECK);
	}

	public function searches ($action) {
		if ($action == 'add') {
			$this->model->saveSearch( $this->input->post('data') );
		} else if ($action == 'remove') {
			$this->model->saveSearch( $this->input->post('data') );
		}
	}

	public function update ($id) {
		
    	$insert = json_decode($this->input->post('data'), true);

    	$this->model->updateWithPK($id, $insert);
   
    	$this->get($id);
	}

	public function remove ($id) {
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();

		$this->model->deleteWithPK($id);

		echo json_encode(['success'=>true,'csrf' => $token,
				'csrf_hash' => $hash], JSON_NUMERIC_CHECK);
	}

	public function add () {
		$insert = json_decode($this->input->post('data'), true);

		$inputs = $this->model->getFields();
		$arr = array();
		foreach ($inputs as $input) {
			if (isset($insert[$input])) {
				$arr[$input] = $insert[$input]; 
			}
		}
		
		$success = $this->model->insertIntoTable($arr);

		echo json_encode( 
			array(
				'csrf' => $token,
				'csrf_hash' => $hash,
				'success' => $success
			)

		, JSON_NUMERIC_CHECK);
	}

	public function addfield () {
		$data = json_decode($this->input->post('data'), true);

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();

		$success = false;
		if ($data['derived']) {
			$success = $this->model->insertDerivedField($data['title'], $data['expression']);
		} else {
			$success = $this->model->insertField($data['title'], $data['kind'], $data['default']);
		}

		echo json_encode( 
			array(
				'csrf' => $token,
				'csrf_hash' => $hash,
				'success' => $success
			)

		, JSON_NUMERIC_CHECK);
	}

	public function removefield () {
		$key = $this->input->post('header');

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();

		$success = $this->model->removeField($key);

		echo json_encode( 
			array(
				'csrf' => $token,
				'csrf_hash' => $hash,
				'success' => $success
			)

		, JSON_NUMERIC_CHECK);
	}

	public function headers () {
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
		echo json_encode( 
			[
				'headers'=>$this->model->getFieldAssociations(),
	    		'csrf' => $token,
				'csrf_hash' => $hash
			]
		);
	}

	public function data () {
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
		$qry = $this->model->get();
		echo json_encode( 
			array(
				'id'=>$this->model->TablePrimaryKey,
				'headers'=>$this->model->getFieldAssociations(),
				'data'=>$qry->result(),
				'csrf' => $token,
				'csrf_hash' => $hash,
				'count' => $qry->num_rows(),
			)

		, JSON_NUMERIC_CHECK);
	}
}

?>