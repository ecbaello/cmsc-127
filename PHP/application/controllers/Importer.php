<?php

class Importer extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('registry_model');
    }

    function index() {
        define('NAV_SELECT', 4);
        $this->load->view('header');
        $this->load->view('import_view');
        $this->load->view('footer');
    }

    function headers() {
        $token = $this->security->get_csrf_token_name();
        $hash = $this->security->get_csrf_hash();

        $table = $this->input->post('table');

        $custom = $this->registry_model->customtable($table);

        if ($custom->num_rows() > 0) {
            $custom = $custom->row();

            $model = new MY_DBmodel;
            $model->ModelTitle = $custom->mdl_name;
            $model->TableName = $table;
            $model->FieldPrefix = $custom->table_prefix; // validate not empty
            $model->init();

            $fields = $model->getFieldAssociations();

            echo json_encode(
                [
                    'headers' => $fields,
                    'csrf' => $token,
                    'csrf_hash' => $hash
                ]
                ,JSON_NUMERIC_CHECK); 
        }
    }

    function tablemaps() {
        $token = $this->security->get_csrf_token_name();
        $hash = $this->security->get_csrf_hash();
        echo json_encode(
                [
                    'tables' => $this->registry_model->models()->result(),
                    'csrf' => $token,
                    'csrf_hash' => $hash
                ]
                ,JSON_NUMERIC_CHECK); 
    }

    function importcsv() {
        $error = '';    //initialize image upload error array to empty

        $config['upload_path'] = 'uploads/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = '1000';

        $this->load->library('upload', $config);

        $token = $this->security->get_csrf_token_name();
        $hash = $this->security->get_csrf_hash();

        $table = $this->input->post('table');

        $success = false;

        if (!empty($table)) {

            $this->load->library('csvreader');

            // If upload failed, display error
            if (!$this->upload->do_upload('csv_db')) {
                $error = $this->upload->display_errors();
            } else {
                $success = true;
                $count = 0;

                $this->load->database();

                $file_data = $this->upload->data();

                $file_path =  'uploads/'.$file_data['file_name'];
                
                $parse = $this->csvreader->parse_file($file_path);

                $size = count($parse);

                if ($parse) {

                    foreach ($parse as $row) {
                        $success = $success && $this->db->insert($table, $row);
                        if ($success) $count++;
                    }

                    //echo "<pre>"; print_r($insert_data);
                } else 
                    $error = "Error occured";


            }

        }

        
    
        echo json_encode(
                [
                    'csrf' => $token,
                    'csrf_hash' => $hash,
                    'success' => $success,
                    'error' => $error,
                    'imported' => $count,
                    'size' => $size
                ]
                ,JSON_NUMERIC_CHECK);           
    }

}
/*END OF FILE*/
