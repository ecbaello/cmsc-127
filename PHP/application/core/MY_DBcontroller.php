<?php

class MY_DBcontroller extends CI_Controller
{

	protected $model = NULL;

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class

		$this->load->library('session');
		$this->load->helper('url');
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
    	echo json_encode( ['data'=>$this->model->getByPK($id),'csrf' => $token,
				'csrf_hash' => $hash], JSON_NUMERIC_CHECK);
	}

	public function search () {
		$query = json_decode($this->input->post('data'), true);

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();

		$db = $this->model->find($query);
		$db = empty($db)?'':$db->result();
		
		echo json_encode( 
			array(
				'id'=>$this->model->TablePrimaryKey,
				'headers'=>$this->model->getFieldAssociations(),
				'data'=>$db,
				'csrf' => $token,
				'csrf_hash' => $hash,
			)

		, JSON_NUMERIC_CHECK);
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
		if (!$this->model->insertIntoTable($arr)){
			show_error('Data Insertion Failed', 400);
		}
	}

	public function addfield () {
		$data = json_decode($this->input->post('data'), true);

		if ($data['derived']) {
			return $this->model->insertDerivedField($data['title'], $data['expression']);
		} else {
			return $this->model->insertField($data['title'], $data['kind'], $data['default']);
		}
	}

	public function removefield () {
		$key = $this->input->post('key');
		$this->model->removeField($key);
	}

	public function headers () {
		echo json_encode( 
			array(
				'headers'=>$this->model->getFieldAssociations()
			)
		);
	}

	public function data () {
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
		echo json_encode( 
			array(
				'id'=>$this->model->TablePrimaryKey,
				'headers'=>$this->model->getFieldAssociations(),
				'data'=>$this->model->get()->result(),
				'csrf' => $token,
				'csrf_hash' => $hash,
			)

		, JSON_NUMERIC_CHECK);
	}
}

?>