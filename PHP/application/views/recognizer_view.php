<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<script type="text/javascript">
	app.constant('recognizerUrl', '<?= isset($url)?$url:current_url() ?>');
</script>
<div ng-controller="recognizer">
	<md-content layout-padding>
	  <md-card class="p-0">
	    <div class="p-3">
			<h5 class="md-headline" flex><i class="fa fa-search"> &nbsp;</i>Recognize foreign tables in the database</h5>
		</div>
			<md-list>
				<md-list-item ng-repeat-start="item in data" class="md-2-line align-items-center secondary-button-padding">
					<h5>{{ item }}</h5>
					<md-button class="md-secondary md-raised md-primary" ng-click="recognize(item)">Recognize</md-button>
				</md-list-item>
				<md-divider ng-repeat-end></md-divider>

			</md-list>
			<md-content class="p-3">
				<em>This only works for indexable tables in your database.</em>
			</md-content>
	  </md-card>
	  <md-card class="p-0">
	    <div class="p-3">
			<h5 class="md-headline" flex><i class="fa fa-check"> &nbsp;</i>Recognized Tables</h5>
		</div>
	    <div>
	    	<table class="table table-striped">
				<tr>
					<th>
						Recognized Table Title
					</th>
					<th>
						Recognized Table Mapping
					</th>
					<th>
						Recognized Table Primary Key
					</th>
					<th>
						
					</th>
				</tr>
				<tr ng-repeat="item in recognized">
					<td>
						{{ item.table_name }}
					</td>
					<td>
						{{ item.mdl_name }}
					</td>
					<td>
						{{ table.table_pk }}
					</td>
					<th class="text-right">
						<md-button class="md-raised md-warn" ng-click="unrecognize(item.table_name)">
							<i class="fa fa-ban"></i>
							Unrecognize
						</md-button>
					</th>
				</tr>
				<tr ng-if="recognized.length == 0" class="text-center">
					<td colspan="4" class="pb-5 pt-5">
						<div style="padding-left: 15%; padding-right: 15%;}}">
							<em>You don't have any foreign tables recognized.</em>
						</div>
					</td>
				</tr>
			</table>
		</div>
	  </md-card>


	</md-content>
	
	<div style="visibility: hidden">
		<div class="md-dialog-container" id="recognizerDialog">
			<md-dialog>
				<md-toolbar>
					<div class="md-toolbar-tools">
						<h2>Recognize Foreign Table</h2>
						<span flex></span>
						<md-button class="md-icon-button" ng-click="closeDialog()">
							<i class="fa fa-times fa-lg"></i>
						</md-button>
					</div>
				</md-toolbar>
				<form ng-cloak name="recognizerForm" ng-submit="recognizeTable(recognizing, title)">
				<md-dialog-content>
						<div class="pt-3" layout-padding>
							<md-input-container class="w-100">
								<input placeholder="Title" type="text" ng-model="title" required>
							</md-input-container>
						</div>
				</md-dialog-content>
				<md-dialog-actions layout="row">
					<md-button type="submit" ng-disabled="!recognizerForm.$valid" class="md-primary md-raised">
						Recognize {{ recognizing }}
					</md-button>
				</md-dialog-actions>
				</form>
			</md-dialog>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/recognizer.js' ?>"></script>