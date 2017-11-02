<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<div ng-controller="tables" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
	<md-select ng-model="select"
			ng-change="redirect()" 
			<?= isset($current_tbl) ? 'ng-init="setSelected(\''.$current_tbl.'\')"':'' ?>
			>
			<md-option ng-repeat="(key, item) in options" ng-value="item">{{ item.title }}</md-option>
	</md-select>
</div>