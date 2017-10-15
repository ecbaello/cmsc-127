<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Required parameters(link, pcf names)

$this->load->library('session');
if(!isset($_SESSION['pcfname']))
	$_SESSION['pcfname'] = 'General';
?>

<script>
var csrf_test_name;
$(document).ready(function(){
	csrf_test_name = $("input[name=csrf_test_name]").val();
    $("#selector").change(function(){
		$.ajax({
         type: "POST",
         url: '<?=$link?>/changePCF', 
         data: {t: $("#selector").val(), 
		 //'<?= $this->security->get_csrf_token_name(); ?>' : 
		 //'<?= $this->security->get_csrf_hash(); ?>'},
		'csrf_test_name':csrf_test_name},
		 success: function(msg){
		 }
		});
	});
});
</script>

<div>
<select id="selector">
<?php
foreach ($pcf_names->result() as $row) {
        echo '<option '.($row->pcf_name==$_SESSION['pcfname'] ? "selected='selected'":"").' value="'.$row->pcf_name.'">'.$row->pcf_name.'</option>';
}

?>
</select>
</div>