<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<link rel="stylesheet" type="text/css" href="<?= base_url().'css/input_style.css' ?>">
<h3>Search</h3>
<select id="model-selector">
	<?php 
	$first = TRUE;
	foreach ($options as $table => $info) {
		echo '<option value="'.$table.'"'.($first?' selected':'').'>'.$info.'</option>';
		$first = FALSE;
	}

	?>
</select>
<input style="border: none; padding: 0; height: 1.5em; box-shadow: none;" id="my-input" type="text">
<script src="<?= base_url().'js/tokenizer.js' ?>"></script>
<script>
	var $input = $('#my-input').tokenizer();
	function submit() {
		var reqString = '';
		var arr = $($input).tokenizer('get');
		if (arr.length === 0) return;
		reqString += arr[0][0]+':'+arr[0][1];
		for (var i = 1; i < arr.length; i++) {
			reqString += ','+arr[i][0]+':'+arr[i][1];
		}
		var reqTable = $('#model-selector').val();
		
	}
</script>
<button class="button" onclick="submit()">Search</button>