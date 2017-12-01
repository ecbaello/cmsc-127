<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('qry_exp'))
{
    function qry_exp($array, $qry_bdr)
    {
    	if (! empty($array['condition'])) return;

        $derived = $array['header']['derived'];
    	$base = $derived ? $array['header']['derivation'] : $array['header']['key'];

    	$expression = $array['values'];

        if (! isset($expression[0])) return;

    	switch ($array['operation']) {
    		case 'range':
    			if (! isset($expression[1])) return;
    			$qry_bdr->where($base.' >=', $expression[0], !$derived);
				$qry_bdr->where($base.' <=', $expression[1], !$derived);
    			break;

    		case 'greater':
    			$qry_bdr->where($base.' >', $expression[0], !$derived);
    			break;

    		case 'lesser':
    			$qry_bdr->where($base.' <', $expression[0], !$derived);
    			break;

    		case 'like':
    			$qry_bdr->like($base, $expression[0], !$derived);
    			break;

    		case 'not_like':
    			$qry_bdr->not_like($base, $expression[0], !$derived);
    			break;

    		case 'not':
    			$qry_bdr->where($base.' !=', $expression[0], !$derived);
    			break;
    		
    		default:
    			$qry_bdr->where($base, $expression[0], !$derived);
    			break;
    	}
    }   
}


if ( ! function_exists('qry_evaluate'))
{
    function qry_evaluate($var, $qry_bdr)
    {
        $condition = $var['condition'];

        $qry_bdr->group_start();
        if ($condition == 'AND' || $condition == 'OR') {

            $j = count($var['rules']);

            for ($i=0; $i < $j; $i++) {

                if ($i==0 || $condition == 'AND' )
                $qry_bdr->group_start();
                else
                    $qry_bdr->or_group_start();

                qry_evaluate($var['rules'][$i], $qry_bdr);

                $qry_bdr->group_end();
            }
            
        } else {
            qry_exp($var, $qry_bdr);
        }  
        $qry_bdr->group_end();
    }   
}

