<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
// $cont_attr?, $swtch, $model, $inp_attr?, $placeholder, $title
?>
<script type="text/javascript">
	app.constant('tableURL', '<?= isset($url)?$url:current_url() ?>');
</script>
<div ng-controller="database" ng-init="rebuild()">
<md-content layout-padding>
  <md-card>
    <md-card-title>
      <span class="md-headline font-weight-bold" flex><?= $title ?></span>
      <span class="table-tools">
      	
      	<md-button ng-init="hideFilter=true" ng-class="hideFilter?'':' md-focused'" class="md-icon-button" ng-click="hideFilter=!hideFilter">
      		<i class="fa fa-filter fa-lg"></i>
      	</md-button>
      	<md-button class="md-icon-button md-primary md-raised" ng-click="showAddDialog($event)">
			<i class="fa fa-plus fa-lg"></i>
		</md-button>
		<md-button class="md-icon-button">
      		<i class="fa fa-print fa-lg"></i>
      	</md-button>
      </span>
    </md-card-title>
    <md-card-content>
			<?php /** filtering **/ ?>
			<div ng-hide="hideFilter" class="filter-card">
				<div class="filter-input">
					<span class="filter-item-or" ng-repeat="(i, orItem) in filter.rules">
						<span class="filter-item-and" ng-repeat="(j, andItem) in orItem.rules">
							<span class="filter-item">
								<span class="filter-field">
									{{ headers[ andItem.header.key ]['title'] }}
								</span>
								<span class="filter-op">
									{{ filterOperations[ andItem.operation ] }}
								</span>
								<span class="filter-value">
									{{ andItem.operation=='range' ?
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
									<md-button class="md-square md-primary" ng-click="removeFilter(i,j)"><i class="fa fa-lg fa-times"></i></md-button>
								</span>
							</span>
						</span>
					</span>
				</div>

				<form class="filter-form">
					<div class="row">
						<div class="col-lg-1 col-sm-12" ng-hide="filter.rules.length==0">
							<md-switch class="caption-switch" ng-model="filterOr">
								<span class="caption-switch-title">
									{{ filterOr?'OR':'AND' }}
								</span>
							</md-switch>
						</div>
						<div class="col-lg-2 col-sm-6">
							<md-select ng-model="newFilter.header" placeholder="Field">
								<md-option ng-repeat="(key, item) in headers" ng-value="key">
									{{ item['title'] }}
								</md-option>
							</md-select>
						</div>
						<div class="col-lg-2 col-sm-6">
							<md-select ng-model="newFilter.operation" placeholder="is">
								<md-option ng-repeat="(key, item) in filterOperations" ng-value="key">
									{{ item }}
								</md-option>
							</md-select>
						</div>
						<div class="col-lg col-sm-12">
							<?= $CI->load->view('input_switcher', 
								[ 
									'swtch' => 'headers[newFilter.header].type',
									'model' => 'newFilter.values[0]',
									'placeholder' => 'Value'
								]
							, true); ?>
						</div>
						<div class="col-lg col-sm-12" ng-if="newFilter.operation=='range'">
							<?= $CI->load->view('input_switcher', 
								[ 
									'swtch' => 'headers[newFilter.header].type',
									'model' => 'newFilter.values[1]',
									'placeholder' => 'To Value'
								]
							, true); ?>
						</div>
						<div class="col-lg-2 col-sm-6">
							<md-button class="md-raised w-100" ng-disabled="!newFilter.header || !newFilter.operation" ng-click="addFilter(filterOr==false)">Add Filter</md-button>
						</div>
						<div class="col-lg-2 col-sm-6">
							<md-button class="md-raised md-primary w-100" ng-disabled="filter.length==0" ng-click="rebuild()">Filter</md-button>
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
									<a href="" ng-click="sort(key)">{{ item['title'] }}</a>
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
		</md-card-content>
  	</md-card>
</md-content>
</div>
