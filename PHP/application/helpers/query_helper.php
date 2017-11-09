<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('qry_exp'))
{
    function qry_exp($array, $qry_bdr)
    {
    	if (! empty($array['condition'])) return;

        $derived = $array['header']['derived'];
    	$base = $derived ? $array['header']['derivation'] : '`'.$array['header']['key'].'`';

    	$expression = $array['values'];

    	switch ($array['operation']) {
    		case 'range':
    			if (! isset($expression[1])) return;
    			$qry_bdr->where($base.' >=', $expression[0], false);
				$qry_bdr->where($base.' <=', $expression[1], false);
    			break;

    		case 'greater':
    			$qry_bdr->where($base.' >', $expression[0], false);
    			break;

    		case 'lesser':
    			$qry_bdr->where($base.' <', $expression[0], false);
    			break;

    		case 'like':
    			$qry_bdr->like($base, $expression[0], false);
    			break;

    		case 'not_like':
    			$qry_bdr->not_like($base, $expression[0], false);
    			break;

    		case 'not':
    			$qry_bdr->where($base.' !=', $expression[0], false);
    			break;
    		
    		default:
    			$qry_bdr->where($base, $expression[0], false);
    			break;
    	}
    }   
}

if ( ! function_exists('qry_and'))
{
    function qry_and($array, $qry_bdr)
    {
        foreach ($array['rules'] as $exp) {
        	qry_exp($exp, $qry_bdr);
        }
    }   
}

if ( ! function_exists('qry_or'))
{
    function qry_or($array, $qry_bdr)
    {   
        $j = count($array['rules']);
        for ($i=0; $i < $j; $i++) { 
            if ($i==0) $qry_bdr->group_start();
            else $qry_bdr->or_group_start();
            qry_and($array['rules'][$i], $qry_bdr);
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

