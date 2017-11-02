<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<div ng-controller="tables" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
	<select ng-options="item as item.title for (key, item) in options" ng-model="select" ng-change="redirect()" <?= isset($current_tbl) ? 'ng-init="setSelected(\''.$current_tbl.'\')"':'' ?> >
	</select>
</div>