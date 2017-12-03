<?php

class Importer extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('registry');
        $this->load->helper('csrf_helper');
    }

    function index() {
        define('NAV_SELECT', 4);
        $this->load->view('header');
        $this->load->view('import_view');
        $this->load->view('footer');
    }

    function headers() {

        $table = $this->input->post('table');

        $custom = $this->registry->customtable($table);

        if ($custom->num_rows() > 0) {
            $custom = $custom->row();

            if (!empty($custom->mdl_class)) {
                $class = $custom->mdl_class;
                $this->load->model($class);
                $fields = $this->$class->getFieldAssociations();
            } else {
                $this->load->model('custom_model');
                $this->custom_model->loadCustom($custom->mdl_name, $table, $custom->table_prefix);
                $fields = $this->custom_model->getFieldAssociations();
            }

            csrf_json_response([
                    'headers' => $fields,
                ]); 
        }
    }

    function tablemaps() {
        csrf_json_response([        
            'tables' => $this->registry->models()->result()
        ]); 
    }

    function importcsv() {
        $error = '';    //initialize image upload error array to empty

        $config['upload_path'] = 'uploads/cache/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = '1000';

        $this->load->library('upload', $config);


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

                $file_path =  $config['upload_path'].$file_data['file_name'];
                
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

                $this->load->helper("file");
                delete_files($config['upload_path']); 
            }

        }

        csrf_json_response([
            'success' => $success,
            'error' => $error,
            'imported' => $count,
            'size' => $size
        ]);

              
    }

}
/*END OF FILE*/
