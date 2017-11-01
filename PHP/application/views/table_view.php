<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="container" ng-controller="database" ng-init="url = '<?= current_url() ?>'">

	<table class="table">
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
						<span ng-switch="item.type" ng-if="!item.read_only">
							<input class="form-control" ng-switch-when="DATE" type="date" ng-model="value[key]">
							<input class="form-control" ng-switch-when="FLOAT" type="number" step="0.01" ng-model="value[key]">
							<input class="form-control" ng-switch-when="NUMBER" type="number" ng-model="value[key]">
							<textarea class="form-control" ng-switch-when="TEXTAREA" ng-model="value[key]"></textarea>
							<input class="form-control" ng-switch-default ng-model="value[key]">
						</span>

					</td>
					<td class="toolbox">
						<button class="btn btn-edit" ng-click="edit(value[idName])"><i class="fa fa-pencil"></i></button>
						<button class="btn btn-edit" ng-click="delete(value[idName])"><i class="fa fa-trash"></i></button>
						<button class="btn btn-confirm" ng-click="send()"><i class="fa fa-check"></i></button>
						<button class="btn btn-confirm" ng-click="cancel()"><i class="fa fa-times"></i></button>
					</td>
			</tr>
		</tbody>
	</table>
	<form>
		<div class="form-group" ng-repeat="(key, item) in headers" ng-if="!item.read_only">
			<label>
				{{item.title}}
			</label>
			<span ng-switch="item.type">
				<input 		class="form-control" ng-switch-when="DATE" 		type="date" 	ng-model="newItem[key]">
				<input 		class="form-control" ng-switch-when="FLOAT" 	type="number" 	step="0.01" 				ng-model="newItem[key]">
				<input 		class="form-control" ng-switch-when="NUMBER" 	type="number" 	ng-model="newItem[key]">
				<textarea 	class="form-control" ng-switch-when="TEXTAREA" 	ng-model="newItem[key]"></textarea>
				<input 		class="form-control" ng-switch-default 			ng-model="newItem[key]">
			</span>
		</div>
		<div class="form-group">
			<button class="btn btn-confirm" ng-click="add()"><i class="fa fa-plus"></i> Add new item</button>
		</div>
	</form>
</div>


