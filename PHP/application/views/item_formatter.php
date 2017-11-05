<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// $item_type, item_value
$CI =& get_instance();
?>
<?= $item_type ?>=='DATE' ? (<?= $item_value ?> | date: "yyyy-MM-dd") : <?= $item_value ?>
<?php 
$CI->load->clear_vars();
?>
