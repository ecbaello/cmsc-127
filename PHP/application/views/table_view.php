<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
// $cont_attr?, $swtch, $model, $inp_attr?, $placeholder
?>
<div ng-controller="database" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
	<?php /** Searching **/ ?>
	<div class="search-card">
		<div class="search-input">
			<span class="search-item-or" ng-repeat="(i, orItem) in search">
				<span class="search-item-and" ng-repeat="(j, andItem) in orItem">
					<span class="search-item">
						<span class="search-field">
							{{ headers[andItem[0]]['title'] }}
						</span>
						<span class="search-op">
							{{ searchOperations[andItem[1][0]] }}
						</span>
						<span class="search-value">
							{{ andItem[1][0]=='range' ?
								andItem[1][1]+'->'+andItem[1][2] :
								andItem[1][1]
							}}
						</span>
						<span style="float: right">
							<md-button class="md-square md-primary" ng-click="removeSearch(i,j)"><i class="fa fa-lg fa-times"></i></md-button>
						</span>
					</span>
				</span>
			</span>
		</div>
		<form class="search-form">
			<div class="row">
				<div class="col-1" ng-if="search.length>0">
					<md-switch ng-model="searchOr">
						{{searchOr?'OR':'AND'}}
					</md-switch>
				</div>
				<div class="col-2">
					<md-select ng-model="newSearch[0]" placeholder="Field">
						<md-option ng-repeat="(key, item) in headers" ng-value="key">
							{{ item['title'] }}
						</md-option>
					</md-select>
				</div>
				<div class="col-2">
					<md-select ng-model="newSearch[1][0]" placeholder="is">
						<md-option ng-repeat="(key, item) in searchOperations" ng-value="key">
							{{ item }}
						</md-option>
					</md-select>
				</div>
				<div class="col">
					<?= $CI->load->view('input_switcher', 
						[ 
							'swtch' => 'headers[newSearch[0]].type',
							'model' => 'newSearch[1][1]',
							'placeholder' => 'Value'
						]
					, true); ?>
				</div>
				<div class="col" ng-if="newSearch[1][0]=='range'">
					<?= $CI->load->view('input_switcher', 
						[ 
							'swtch' => 'headers[newSearch[0]].type',
							'model' => 'newSearch[1][2]',
						]
					, true); ?>
				</div>
				<div class="col-1">
					<md-button ng-click="addSearch(searchOr==false)">Add</md-button>
				</div>
				<div class="col-1">
					<md-button class="md-raised md-primary" ng-click="goSearch()">Search</md-button>
				</div>
			</div>
		</form>
	</div>
	<?php /** Table **/ ?>
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
					<tr ng-repeat="(index, value) in data" ng-class="{'row-update' : (index==editIndex && isEdit), 'row-edit' : (index==editIndex)}">
							<td ng-repeat="(key, item) in headers">
								<span ng-class="item.read_only?'':'cell-value'">
									{{ item.type=='DATE' ? (value[key] | date: "yyyy-MM-dd") : value[key] }}
								</span>
								<?= $CI->load->view('input_switcher', 
									[ 
										'swtch' => 'item.type',
										'model' => 'value[key]',
										'cont_attr' => 'ng-if="!item.read_only"'
									]
								, true); ?>
							</td>
							<td class="toolbox">
								<md-button class="btn-edit md-square md-primary" ng-click="edit(index)"><i class="fa fa-pencil"></i></md-button>
								<md-button class="btn-edit md-square md-warn" ng-click="delete(index)"><i class="fa fa-trash"></i></md-button>
								<md-button class="btn-confirm md-square md-raised md-accent" ng-click="send()"><i class="fa fa-check"></i></md-button>
								<md-button class="btn-confirm md-square md-raised md-warn" ng-click="cancel()"><i class="fa fa-times"></i></md-button>
							</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	<?php /** Adding Items **/ ?>
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
								<?= $CI->load->view('input_switcher', 
									[ 
										'swtch' => 'item.type',
										'model' => 'newItem[key]',
										'label' => '{{item.title}}'
									]
								, true); ?>
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


