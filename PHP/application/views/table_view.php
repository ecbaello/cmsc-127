<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div ng-controller="database" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
	<div id="container">
			<form>
			<md-progress-linear md-mode="indeterminate" ng-disabled="!serverRequesting"></md-progress-linear>
			<table class="table" id="db-table">
				<thead>
					<tr>
						<th ng-repeat="(key, item) in headers">
							{{ item['title'] }}
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="(id, value) in data" ng-class="{'row-update' : (value[idName]==editIndex && isEdit), 'row-edit' : (value[idName]==editIndex)}">
							<td ng-repeat="(key, item) in headers">
								<span ng-class="item.read_only?'':'cell-value'">
									{{ item.type=='DATE' ? (value[key] | date: "yyyy-MM-dd") : value[key] }}
								</span>
								<md-input-container ng-switch="item.type" ng-if="!item.read_only">
									<md-datepicker ng-switch-when="DATE" type="date" ng-model="value[key]"></md-datepicker>
									<input ng-switch-when="FLOAT" type="number" step="0.01" ng-model="value[key]">
									<input ng-switch-when="NUMBER" type="number" ng-model="value[key]">
									<textarea ng-switch-when="TEXTAREA" ng-model="value[key]"></textarea>
									<input ng-switch-default ng-model="value[key]">
								</md-input-container>

							</td>
							<td class="toolbox">
								<md-button class="btn-edit md-square md-primary" ng-click="edit(value[idName])"><i class="fa fa-pencil"></i></md-button>
								<md-button class="btn-edit md-square md-warn" ng-click="delete(value[idName])"><i class="fa fa-trash"></i></md-button>
								<md-button class="btn-confirm md-square md-raised md-accent" ng-click="send()"><i class="fa fa-check"></i></md-button>
								<md-button class="btn-confirm md-square md-raised md-warn" ng-click="cancel()"><i class="fa fa-times"></i></md-button>
							</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	<md-button class="md-primary md-raised" ng-click="showAddDialog($event)">
		<i class="fa fa-plus"></i> Create Item
	</md-button>
	<div style="visibility: hidden">
		<div class="md-dialog-container" id="addDialog">
			<md-dialog>
				<md-toolbar>
					<div class="md-toolbar-tools">
						<h2>Create Item</h2>
						<span flex></span>
						<md-button class="md-icon-button" ng-click="closeDialog()">
							<i class="fa fa-times fa-lg"></i>
						</md-button>
					</div>
				</md-toolbar>
				<md-dialog-content>
					<form ng-cloak>
						<div layout-padding>
							<div ng-repeat="(key, item) in headers" ng-if="!item.read_only" class="md-block">
								<md-input-container>
									<label>
										{{item.title}}
									</label>
									<span ng-switch="item.type">
										<md-datepicker ng-switch-when="DATE" 		type="date" 	ng-model="newItem[key]"></md-datepicker>
										<input 		ng-switch-when="FLOAT" 	type="number" 	step="0.01" 				ng-model="newItem[key]">
										<input 		ng-switch-when="NUMBER" 	type="number" 	ng-model="newItem[key]">
										<textarea 	ng-switch-when="TEXTAREA" 	ng-model="newItem[key]"></textarea>
										<input 		ng-switch-default 			ng-model="newItem[key]">
									</span>
								</md-input-container>
							</div>
						</div>
					</form>
				</md-dialog-content>
				<md-dialog-actions layout="row">
					<md-button class="btn-confirm md-raised md-primary" ng-click="add()"><i class="fa fa-save"></i> Save</md-button>
				</md-dialog-actions>
			</md-dialog>
		</div>
	</div>
</div>


