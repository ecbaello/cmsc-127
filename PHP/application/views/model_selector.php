<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<div ng-controller="selector">
	<md-content layout-padding>
	  <md-card>
	    <md-card-title>
	      <span class="md-headline">Select Table</span>
	    </md-card-title>
	    <md-card-content>
			<div ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
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
	<?php if ($permission >= PERMISSION_ALTER): ?>
	<md-content layout-padding>
	  <md-card class="p-0">
	    <md-toolbar>
			<h5 class="md-toolbar-tools">Category Tools</h5>
		</md-toolbar>
	    <div ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
			<md-list>
				<md-list-item class="md-2-line align-items-center" ng-repeat-start="(key, item) in options" class="secondary-button-padding">
					<p class="d-inline">{{item.title}}</p>
					<md-button class="md-secondary md-raised md-warn" ng-click="removeCategory(item.title)">Delete</md-button>
				</md-list-item>
				<md-divider ng-repeat-end></md-divider>
			</md-list>
			<form class="p-4" ng-submit="addCategory(category)">
				<h5>
				New Category
				</h5>
				<md-input-container>
					<input placeholder="Title" ng-model="category" required>

				</md-input-container>
				<md-button class="md-raised md-primary" type="submit">
					Add New
				</md-button>
			</form>
		</div>
	  </md-card>
	</md-content>
	<?php endif ?>
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/selector.js' ?>"></script>