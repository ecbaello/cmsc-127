<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<md-content layout-padding>
  <md-card>
    <md-card-title>
      <span class="md-headline">Select Table</span>
    </md-card-title>
    <md-card-content>
		<div ng-controller="tableSelector" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
			<md-select placeholder="Select table" ng-model="select" ng-change="redirect()" 
					<?= isset($current_tbl) ? 'ng-init="setSelected(\''.$current_tbl.'\')"':'' ?>
					>
					<md-option ng-repeat="value in data" ng-value="value">{{value}}</md-option>
			</md-select>
		</div>
	</md-card-content>
  </md-card>
</md-content>