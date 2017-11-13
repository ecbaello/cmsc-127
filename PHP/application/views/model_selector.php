<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<md-content layout-padding>
  <md-card>
    <md-card-title>
      <span class="md-headline">Select Table</span>
    </md-card-title>
    <md-card-content>
		<div ng-controller="selector" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
			<md-select placeholder="Select table" ng-model="select"
					ng-change="redirect()" 
					<?= isset($current_tbl) ? 'ng-init="setSelected(\''.$current_tbl.'\')"':'' ?>
					>
					<md-option ng-repeat="(key, item) in options" ng-value="item">{{ item.title }}</md-option>
			</md-select>
		</div>
	</md-card-content>
  </md-card>
</md-content>
<script type="text/javascript" src="<?= base_url().'js/controllers/selector.js' ?>"></script>