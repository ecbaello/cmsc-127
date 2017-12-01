<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// $cont_attr?, $swtch, $model, $inp_attr?, $placeholder, $label

$CI =& get_instance();

if (!isset($cont_attr)) $cont_attr = '';
if (!isset($inp_attr)) $inp_attr = '';
if (!isset($required)) $required = '';
else $required = 'ng-required = "'.$required.'" ';

$init = isset($initialize);

$init_st = $init?'ng-init="'.$model.'='.$initialize.'" ':'';

$placeholder = (isset($placeholder)?'placeholder = "'.$placeholder.'"':'').' ';
$modelstr = 'ng-model = "'.$model.'" ';

$inp_attr .= ' ';

$swtch = 'ng-switch = "'.$swtch.'" ';
$cont_attr .= ' ';

$input = $modelstr.$placeholder.$inp_attr;
$container = $swtch.$cont_attr;
$label = isset($label)?'<label>'.$label.'</label>':'';

?>
<md-input-container <?= $container ?> >
	<?= $label ?>
	<span ng-switch-when="DATE">
		<md-datepicker type="date" <?=
			$init?'ng-init="'.$model.'=\''.date('c').'\'"':''
			?> <?= $input ?>></md-datepicker>
	</span>
	<span ng-switch-when="FLOAT">
		<input type="number" step="0.01" <?= $input.$required.$init_st ?> >
	</span>
	<span ng-switch-when="NUMBER">
		<input type="number" <?= $input.$required.$init_st ?> >
	</span>
	<span ng-switch-when="TEXTAREA">
		<textarea <?= $input.$required.$init_st ?> ></textarea>
	</span>
	<span ng-switch-when="NUMERIC">
		<input type="number" step="0.000001" <?= $input.$required.$init_st ?> ></input>
	</span>
	<span ng-switch-when="URL">
		<input type="url" <?= $input.$required.$init_st ?> >
	</span>
	<span ng-switch-when="EMAIL">
		<input type="email" <?= $input.$required.$init_st ?> >
	</span>
	<span ng-switch-default>
		<input <?= $input.$required.$init_st ?> >
	</span>
</md-input-container>

<?php
$CI->load->clear_vars();
?>

