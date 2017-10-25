<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Required parameters(link, fields)

$newline		= "\n";

$this->load->helper('form');
?>

<?php echo form_open($link); ?>
	<div class="grid-x grid-padding-x">
		<?php if ( isset($tablename) ) echo '<input type="hidden" name="table" value='.$tablename.'>'.$newline ?>
		<?php if ( isset($extrahtml) ) echo $extrahtml.$newline ?>
		<?php
			if (!empty($constants) && is_array($constants))
				foreach ($constants as $key => $value) {
					echo '<input type="hidden" name="'.$key.'" value="'.$value.'">'.$newline;
				}
			foreach ($fields as $key => $value) {
				echo '<div class="small-12 medium-6 large-6 cell">'.$newline;
				echo '<label for="'.$key .'">'.$value[TBL_TITLE];
				switch ($value[TBL_INPUT]) {
					case 'PASSWORD':
						echo '<input type="password" data-validation="length alphanumeric strength" data-validation-length="min20" data-validation-allowing="-_" data-validation-strength="2" name="'.$key.'" >';
						break;

					case 'URL':
						echo '<input type="url" data-validation="url" name="'.$key.'" >';
						break;

					case 'EMAIL':
						echo '<input type="email" data-validation="email" name="'.$key.'" >';
						break;

					case 'CHECKBOX':
						echo '<input type="checkbox" name="'.$key.'" >';
						break;

					case 'TEXTAREA':
						echo '<textarea name="'.$key.'" ></textarea>';
						break;

					case 'DATE':
						echo '<input type="date" data-validation="date" name="'.$key.'" >';
						break;

					case 'NUMBER':
						echo '<input type="number" data-validation="number" name="'.$key.'" >';
						break;

					case 'FLOAT':
						echo '<input type="number" step="0.01" data-validation="number" data-validation-allowing="float" name="'.$key.'" >';
						break;
					
					default:
						echo '<input type="text" name="'.$key.'" >';
						break;
				}
				
				echo '</label>'.$newline;
				echo '</div>'.$newline;
			}
		?>
	</div>
	<div class="grid-padding-x" style="text-align: center;"><input class="button" style="width: 100%" type="submit" value="Insert"></div>
<?php echo form_close(); ?>
<script type="text/javascript">
	 $.validate({
	    modules : 'html5, date, security'
	 });
</script>

