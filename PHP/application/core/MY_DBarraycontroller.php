	<?php

class MY_DBarraycontroller extends CI_Controller {

	public $model = NULL;

	public function __construct()
	{
		parent::__construct(); // do constructor for parent class
		
		
	}

	public function index() {
		$this->load->view('header');

		$this->load->view('html', array('html'=>
			'<h2 class="view-title">'.$this->model->ModelTitle.'</h2>'
		));
		$this->makeSelector();
		
		$this->load->view('footer');
	}

	protected function makeSelector($table = null, $replacelink = null) {
		$settings = array();

		if (!empty($table))
			$settings['current_tbl'] = $this->model->convertNameToCategory($table);

		if (!empty($replacelink)) 
			$settings['url'] = $replacelink;
		
		$this->load->view('model_selector', $settings);
	}

	public function table($subtable = null, $action = null, $id = null, $other = null) {
		
		if ($subtable !== null) {
			$subtable = urldecode($subtable);
			if ($action === null) $this->makeHTML($subtable);
			else {
				switch ($action) {
					case 'add':
						$this->add($subtable);
						break;
					case 'get':
						if ($id !== null) $this->get($subtable, $id);
						else show_404();
						break;
					case 'update':
						if ($id !== null) $this->update($subtable, $id);
						else show_404();
						break;
					case 'remove':
						if ($id !== null) $this->remove($subtable, $id);
						else show_404();
						break;
					case 'data':
						$this->data($subtable);
						break;
					case 'search':
						$this->search($subtable);
						break;
					
					default:
						show_404();
						break;
				}


			}
		} else {
			$array = array();
			foreach ($this->model->getCategories() as $key => $value) {
				$arr = array();
				$arr['title'] = $value;
				$arr['link'] = urlencode($value);
				$array[$key] = $arr;
			}
			echo json_encode(['data'=>$array]);
		}
	}

	protected function makeHTML($subtable)
	{

	}

	protected function data($table) {
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
		echo json_encode( 
			array(
				'id'=>$this->model->TablePrimaryKey,
				'headers'=>$this->model->getFieldAssociations(),
				'data'=>$this->model->getCategoryTable($table)->result(),
				'csrf' => $token,
				'csrf_hash' => $hash)
		, JSON_NUMERIC_CHECK);
	}

	public function search ($table) {
		$query = json_decode($this->input->post('data'), true);

		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
		echo json_encode( 
			array(
				'id'=>$this->model->TablePrimaryKey,
				'headers'=>$this->model->getFieldAssociations(),
				'data'=>$this->model->find($query, $table)->result(),
				'csrf' => $token,
				'csrf_hash' => $hash,
			)

		, JSON_NUMERIC_CHECK);
	}

	protected function add($subtable) {
		$insert = json_decode($this->input->post('data'), true);

		if (!empty($insert)) {
			$inputs = $this->model->getFields();
			$arr = array();
			foreach ($inputs as $input) {
				if (isset($insert[$input])) {
					$arr[$input] = $insert[$input]; 
				}
			}
			if (!$this->model->insertIntoCategoryTable($subtable, $arr)){
				show_error('The database doesn\'t accept the input. Check the format of your input.', 400);
			}
		} else {
			show_error('The insertion request is empty.', 406);
		}
	}

	protected function get($subtable, $id) {
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();
    	echo json_encode( ['data'=>$this->model->getIndividual($id),'csrf' => $token,
				'csrf_hash' => $hash], JSON_NUMERIC_CHECK);
	}

	protected function remove($subtable, $id) {
		$token = $this->security->get_csrf_token_name();
		$hash = $this->security->get_csrf_hash();

		$this->model->deleteFromCategoryTable($subtable, $id);

		echo json_encode(['success'=>true,'csrf' => $token,
				'csrf_hash' => $hash], JSON_NUMERIC_CHECK);
	}

	protected function update($subtable, $id) {
		$insert = json_decode($this->input->post('data'), true);

    	$this->model->updateOnCategoryTable($subtable, $id, $insert);

    	$this->get($subtable, $id);
	}
}