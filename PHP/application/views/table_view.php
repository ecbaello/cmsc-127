<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
// $cont_attr?, $swtch, $model, $inp_attr?, $placeholder, $title
?>
<md-content layout-padding>
  <md-card>
    <md-card-title>
      <span class="md-headline"><?= $title ?></span>
    </md-card-title>
    <md-card-content>
		<div ng-controller="database" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
			<?php /** Searching **/ ?>
			<div class="search-card">
				<div class="search-input">
					<span class="search-item-or" ng-repeat="(i, orItem) in search.rules">
						<span class="search-item-and" ng-repeat="(j, andItem) in orItem.rules">
							<span class="search-item">
								<span class="search-field">
									{{ headers[ andItem.header.key ]['title'] }}
								</span>
								<span class="search-op">
									{{ searchOperations[ andItem.operation ] }}
								</span>
								<span class="search-value">
									{{ andItem[1][0]=='range' ?
										(<?= $CI->load->view('item_formatter', 
										[ 
											'item_type' => "headers[andItem.header.key]['type']",
											'item_value' => 'andItem.values[0]'
										]
										, true); ?>)
										+' to '+
										(<?= $CI->load->view('item_formatter', 
										[ 
											'item_type' => "headers[andItem.header.key]['type']",
											'item_value' => 'andItem.values[1]'
										]
										, true); ?>) :
										(<?= $CI->load->view('item_formatter', 
										[ 
											'item_type' => "headers[andItem.header.key]['type']",
											'item_value' => 'andItem.values[0]'
										]
										, true); ?>)
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
						<div class="col-lg-1 col-sm-12" ng-hide="search.rules.length==0">
							<md-switch class="caption-switch" ng-model="searchOr">
								<span class="caption-switch-title">
									{{ searchOr?'OR':'AND' }}
								</span>
							</md-switch>
						</div>
						<div class="col-lg-2 col-sm-6">
							<md-select ng-model="newSearch.header" placeholder="Field">
								<md-option ng-repeat="(key, item) in headers" ng-value="key">
									{{ item['title'] }}
								</md-option>
							</md-select>
						</div>
						<div class="col-lg-2 col-sm-6">
							<md-select ng-model="newSearch.operation" placeholder="is">
								<md-option ng-repeat="(key, item) in searchOperations" ng-value="key">
									{{ item }}
								</md-option>
							</md-select>
						</div>
						<div class="col-lg col-sm-12">
							<?= $CI->load->view('input_switcher', 
								[ 
									'swtch' => 'headers[newSearch.header.key].type',
									'model' => 'newSearch.values[0]',
									'placeholder' => 'Value'
								]
							, true); ?>
						</div>
						<div class="col-lg col-sm-12" ng-if="newSearch.operation=='range'">
							<?= $CI->load->view('input_switcher', 
								[ 
									'swtch' => 'headers[newSearch.header.key].type',
									'model' => 'newSearch.values[1]',
									'placeholder' => 'To Value'
								]
							, true); ?>
						</div>
						<div class="col-lg-2 col-sm-6">
							<md-button class="md-raised w-100" ng-disabled="!newSearch.header || !newSearch.operation" ng-click="addSearch(searchOr==false)">Add Query</md-button>
						</div>
						<div class="col-lg-2 col-sm-6">
							<md-button class="md-raised md-primary w-100" ng-disabled="search.length==0" ng-click="goSearch()">Search</md-button>
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
											{{
											<?= $CI->load->view('item_formatter', 
											[ 
												'item_type' => 'item.type',
												'item_value' => 'value[key]'
											]
											, true); ?> 
											 }}
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
										<?php echo $CI->load->view('input_switcher', 
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
	</md-card-content>
  </md-card>
</md-content>
