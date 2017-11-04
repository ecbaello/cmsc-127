<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// $cont_attr?, $swtch, $model, $inp_attr?, $placeholder, $label

if (!isset($cont_attr)) $cont_attr = '';
if (!isset($inp_attr)) $inp_attr = '';

$placeholder = (isset($placeholder)?'placeholder = "'.$placeholder.'"':'').' ';
$model = 'ng-model = "'.$model.'" ';
$inp_attr .= ' ';

$swtch = 'ng-switch = "'.$swtch.'" ';
$cont_attr .= ' ';

$input = $model.$placeholder.$inp_attr;
$container = $swtch.$cont_attr;
$label = isset($label)?'<label>'.$label.'</label>':'';

?>
<md-input-container <?= $container ?> >
	<?= $label ?>
	<span ng-switch-when="DATE">
		<md-datepicker type="date" <?= $input ?> ></md-datepicker>
	</span>
	<span ng-switch-when="FLOAT">
		<input type="number" step="0.01" <?= $input ?> >
	</span>
	<span ng-switch-when="NUMBER">
		<input type="number" <?= $input ?> >
	</span>
	<span ng-switch-when="TEXTAREA">
		<textarea <?= $input ?> ></textarea>
	</span>
	<span ng-switch-default>
		<input <?= $input ?> >
	</span>
</md-input-container>

