<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// $item_type, item_value
$CI =& get_instance();
?>
<?= $item_type ?>=='DATE' ? (<?= $item_value ?> | date: "MM/dd/yyyy") : <?= $item_value ?>
<?php 
$CI->load->clear_vars();
?>
