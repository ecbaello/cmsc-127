<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('qry_exp'))
{
    function qry_exp($array, $qry_bdr)
    {
    	if (! isset($array[0])) return;

    	$base = $array[0];

    	if (! isset($array[0]) || ! isset($array[1])) return;

    	$expression = $array[1];

    	switch ($expression[0]) {
    		case 'range':
    			if (! isset($expression[2])) return;
    			$qry_bdr->where($base.' >=', $expression[1]);
				$qry_bdr->where($base.' <=', $expression[2]);
    			break;

    		case 'greater':
    			$qry_bdr->where($base.' >', $expression[1]);
    			break;

    		case 'lesser':
    			$qry_bdr->where($base.' <', $expression[1]);
    			break;

    		case 'like':
    			$qry_bdr->like($base, $expression[1]);
    			break;

    		case 'not_like':
    			$qry_bdr->not_like($base, $expression[1]);
    			break;

    		case 'not':
    			$qry_bdr->where($base.' !=', $expression[1]);
    			break;
    		
    		default:
    			$qry_bdr->where($base, $expression[1]);
    			break;
    	}
    }   
}

if ( ! function_exists('qry_and'))
{
    function qry_and($array, $qry_bdr)
    {
        foreach ($array as $exp) {
        	qry_exp($exp, $qry_bdr);
        }
    }   
}

if ( ! function_exists('qry_or'))
{
    function qry_or($array, $qry_bdr)
    {
        for ($i=0; $i < count($array); $i++) { 
            if ($i==0) $qry_bdr->group_start();
            else $qry_bdr->or_group_start();
            qry_and($array[$i], $qry_bdr);
            $qry_bdr->group_end();
        }
        
    }   
}


if ( ! function_exists('qry_evaluate'))
{
    function qry_evaluate($var, $qry_bdr)
    {
        $qry_bdr->group_start();
        qry_or($var, $qry_bdr);
        $qry_bdr->group_end();
    }   
}

