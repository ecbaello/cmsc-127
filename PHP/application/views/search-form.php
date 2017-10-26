<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// To get csrf
$ci =& get_instance();
$token = $ci->security->get_csrf_token_name();
$hash = $ci->security->get_csrf_hash();

?>
<link rel="stylesheet" type="text/css" href="<?= base_url().'css/input_style.css' ?>">
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
		// Post to the provided URL with the specified parameters.
		var form = $('<form></form>');

	    form.attr("method", "post");
	    form.attr("action", "<?php echo $path ; ?>");

	    var field = $('<input></input>');

	    field.attr("type", "hidden");
        field.attr("name", "<?php echo SRCH_TABLE ?>");
        field.attr("value", reqTable);

	    form.append(field);

	    var field2 = $('<input></input>');

	    field2.attr("type", "hidden");
        field2.attr("name", "<?php echo SRCH_QRY ?>");
        field2.attr("value", reqString);

	    form.append(field2);

	    var field3 = $('<input></input>');

	    field3.attr("type", "hidden");
        field3.attr("name", "<?php echo $token ?>");
        field3.attr("value", "<?php echo $hash ?>");

	    form.append(field3);

	    // The form needs to be a part of the document in
	    // order for us to be able to submit it.
	    $(document.body).append(form);
	    form.submit();
	}
</script>
<button class="button" onclick="submit()">Search</button>