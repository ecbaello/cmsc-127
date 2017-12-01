<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('csrf_json_response'))
{
    function csrf_json_response($data)
    {
    	$CI =& get_instance();
        $data['csrf'] = $CI->security->get_csrf_token_name();
        $data['csrf_hash'] = $CI->security->get_csrf_hash();
        echo json_encode (
            $data,
            JSON_NUMERIC_CHECK
        );
    }   
}

