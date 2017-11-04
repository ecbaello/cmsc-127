<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$qry_CI =& get_instance();
$qry_CI->load->database();

if ( ! function_exists('qry_exp'))
{
    function qry_exp($array)
    {
    	if (! isset($array[0])) return;

    	$base = $array[0];

    	if (! isset($array[0]) || ! isset($array[1])) return;

    	$expression = $array[1];

    	switch ($expression[0]) {
    		case 'range':
    			if (! isset($expression[2])) return;
    			$this->db->where($base.' >=', $expression[1]);
				$this->db->where($base.' <=', $expression[2]);
    			break;

    		case 'greater':
    			$this->db->where($base.' >', $expression[1]);
    			break;

    		case 'lesser':
    			$this->db->where($base.' <', $expression[1]);
    			break;

    		case 'like':
    			$this->db->like($base, $expression[1]);
    			break;

    		case 'not_like':
    			$this->db->not_like($base, $expression[1]);
    			break;

    		case 'not':
    			$this->db->where($base.' !=', $expression[1]);
    			break;
    		
    		default:
    			$this->db->where($base, $expression[1]);
    			break;
    	}
    }   
}

if ( ! function_exists('qry_and'))
{
    function qry_and($array)
    {
    	$qry_CI->and_group_start();
        foreach ($array as $exp) {
        	qry_exp($exp);
        }
        $qry_CI->group_end();
    }   
}

if ( ! function_exists('qry_or'))
{
    function qry_or($array)
    {
    	$qry_CI->or_group_start();
        foreach ($array as $ands) {
        	qry_and($ands);
        }
        $qry_CI->group_end();
    }   
}


if ( ! function_exists('qry_evaluate'))
{
    function qry_evaluate($var)
    {
        qry_or($var);
    }   
}

