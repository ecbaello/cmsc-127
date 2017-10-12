<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<h3>Search</h3>
<input style="border: none; padding: 0; height: 1.5em; box-shadow: none;" id="my-input" type="text">
<link rel="stylesheet" type="text/css" href="<?= base_url().'css/input_style.css' ?>">
<script src="<?= base_url().'js/tokenizer.js' ?>"></script>
<script>
	var $input = $('#my-input').tokenizer();
</script>