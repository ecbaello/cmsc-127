<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Required parameters(link, fields)

$newline		= "\n";

$this->load->helper('form');
?>
<div style="float: right; position: relative;">
	<button class="btn fa fa-plus round-top-right" onclick="$('#container .input-form').fadeToggle(100);"></button>
</div>
<div class="input-form" style="display: none">
	
<?php echo form_open($link, array ('class'=>'button-wrapper')); ?>
<button class="btn form-close alert fa fa-close round-top-right" onclick="$('#container .input-form').fadeToggle(100);"></button>
<div class="form-wrapper">
	<h3 class="form-title">Insert Data</h3>
	<div class="grid-x grid-padding-x">
		<?php if ( isset($tablename) ) echo '<input type="hidden" name="table" value='.$tablename.'>'.$newline ?>
		<?php if ( isset($extrahtml) ) echo $extrahtml.$newline ?>
		<?php
			if (!empty($constants) && is_array($constants))
				foreach ($constants as $key => $value) {
					echo '<input type="hidden" name="'.$key.'" value="'.$value.'">'.$newline;
				}
				$post = '';
			foreach ($fields as $key => $value) {
				$echonow = TRUE;
				$sb = '';
				$sb.= '<div class="small-12 medium-6 large-6 cell">'.$newline;
				$sb.= '<label for="'.$key .'">'.$value[TBL_TITLE];
				switch ($value[TBL_INPUT]) {
					case 'PASSWORD':
						$sb.= '<input type="password" data-validation="length alphanumeric strength" data-validation-length="min8" data-validation-allowing="-_" data-validation-strength="2" name="'.$key.'" >';
						break;

					case 'URL':
						$sb.= '<input type="url" data-validation="url" name="'.$key.'" >';
						break;

					case 'EMAIL':
						$sb.= '<input type="email" data-validation="email" name="'.$key.'" >';
						break;

					case 'CHECKBOX':
						$sb.= '<input type="checkbox" name="'.$key.'" >';
						break;

					case 'TEXTAREA':
						$sb.= '<textarea name="'.$key.'" ></textarea>';
						$echonow = FALSE;
						break;

					case 'DATE':
						$sb.= '<input type="date" data-validation="date" name="'.$key.'" >';
						break;

					case 'NUMBER':
						$sb.= '<input type="number" data-validation="number" name="'.$key.'" >';
						break;

					case 'FLOAT':
						$sb.= '<input type="number" step="0.01" data-validation="number" data-validation-allowing="float" name="'.$key.'" >';
						break;
					
					default:
						$sb.= '<input type="text" name="'.$key.'" >';
						break;
				}
				
				$sb.= '</label>'.$newline;
				$sb.= '</div>'.$newline;

				if ($echonow) echo $sb;
				else $post.=$sb;
			}
			echo $post;
		?>
	</div>
	<div class="grid-padding-x" style="text-align: center;"><input class="button" style="width: 100%" type="submit" value="Insert"></div>
	</div>
<?php echo form_close(); ?>
<script type="text/javascript">
	 $.validate({
	    modules : 'html5, date, security'
	 });
</script>


</div>

