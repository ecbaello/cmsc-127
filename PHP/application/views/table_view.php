<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$CI->load->model('permission_model');
if (!isset($permission)) $permission = -1;
// $cont_attr?, $swtch, $model, $inp_attr?, $placeholder, $title
?>
<script type="text/javascript">
	app.constant('tableURL', '<?= isset($url)?$url:current_url() ?>');
</script>
<?php if ($permission >= PERMISSION_PUBLIC): ?>
<div ng-controller="database" ng-init="rebuild(true)" layout-padding>
	<md-card class="p-0">
		<md-progress-linear md-mode="indeterminate" ng-disabled="!serverRequesting"></md-progress-linear>
		<md-card-title>
			<h2 class="md-headline font-weight-bold" flex><?= $title ?></h2>
			<span class="table-tools">
				<div>
					<?php if ($permission >= PERMISSION_CHANGE): ?>
					<md-button ng-class="!multiEdit?'':' md-primary'" class="md-icon-button" ng-click="multiEdit=!multiEdit; cancel()">
						<i class="fa fa-check-square-o fa-lg"></i>
					</md-button>
					<?php endif ?>
					<md-button ng-init="hideFilter=true" ng-class="hideFilter?'':' md-primary'" class="md-icon-button" ng-click="hideFilter=!hideFilter; cancel()">
						<i class="fa fa-filter fa-lg"></i>
					</md-button>
					<?php if ($permission >= PERMISSION_ADD): ?>
					<md-button class="md-icon-button md-primary md-raised" ng-click="showDialog($event, 'addDialog', true)">
						<i class="fa fa-plus fa-lg"></i>
					</md-button>
					<?php endif ?>
				</div>
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
							<md-button class="md-raised md-primary w-100" ng-disabled="filter.length==0" ng-click="goFilter()">Filter</md-button>
						</div>
					</div>
				</form>
				<?php if ($permission >= PERMISSION_LOGIN): ?>
				<div class="pl-3 pr-3">
					<h5>
						Available Filters
					</h5>
					<div class="filters" ng-init="loadFilters()">
						<md-button class="md-raised md-accent" ng-click="showDialog($event, 'filterManager', true)">Manage...</md-button>
						<md-button ng-disabled="filter.rules.length == 0" class="md-raised md-primary" ng-click="showDialog($event, 'filterNameDialog', false)">Save...</md-button>
						|
						<span ng-repeat="(id, filter) in userFilters" >
							<md-button class="md-accent" ng-disabled="currentUserFilterId==id" ng-click="filterChanged(id)">{{ filter.query_title }}</md-button>
						</span>
						<em class="font-weight-italic" ng-if="(userFilters|keyLength)==0"> &nbsp; Add to the available filters by clicking on save.</em>
					</div>
				</div>
				<?php endif ?>
				<div class="row" layout-padding>
					<div class="col-lg-11 col-md-10 col-sm-8 text-right">
						<md-button class="mr-3 md-raised" ng-click="cleanFilter()">Clear Filter</md-button>
						<span>Limit items by</span>
					</div>
					<div class="col-lg-1 col-md-2 col-sm-4 align-items-center">
						<md-select class="mt-2 ml-0 mr-0 mb-0" ng-model="limit" ng-change="rebuild(false)" placeholder="Entries by">
							<md-option ng-repeat="limiter in limitOptions" ng-value="limiter">
								<em>{{ limiter }}</em>
							</md-option>
						</md-select>
					</div>
				</div>
			</div>
			<div layout="row" class="p-0" layout-padding>
				<div layout-fill class="align-middle">
					<span class="">Page <strong>{{ page + 1 }}</strong> of <strong>{{ (fetchableCount / limit) | page }}</strong>, {{ fetchableCount }} item{{ fetchableCount>1?'s':'' }}</span>
					
					<span ng-hide="limit>=fetchableCount">
						<span class="ml-3" ng-hide="!onJump">
							<strong> Jump to </strong>
							<md-input-container class="m-0 mini" style="width: 3em">
								<input type="number" ng-model="nextpage">
							</md-input-container>
						</span>
						<md-button class="md-icon-button {{onJump?'md-raised md-primary':''}}" ng-click="jump()">
							<i class="fa fa-bolt"></i>
						</md-button>
					</span>
				</div>
				<div flex></div>
				<div ng-if="limit<fetchableCount" class="text-right" layout-fill>
					<md-button class="md-accent md-raised md-icon-button" ng-click="navigate(false)">
						<i class="fa fa-chevron-left"></i>
					</md-button>
					<md-button class="md-accent md-raised md-icon-button" ng-click="navigate(true)">
						<i class="fa fa-chevron-right"></i>
					</md-button>
				</div>
			</div>
			<?php if ($permission >= PERMISSION_CHANGE): ?>
			<div ng-hide="!multiEdit">
				<div class="pl-2">Perform action on selected rows</div>
				<md-button class="md-raised md-primary" ng-click="downloadSelected()">
					Download
				</md-button>
				<md-button class="md-raised md-warn" ng-click="performSelectAction('remove', true)">
					Delete
				</md-button>
				
			</div>
			<?php endif ?>
			<?php /** Table **/ ?>
			<div id="container">
				
					<table class="table table-striped table-bordered table-hover" ng-class="{'table-sm':(!$mdMedia('gt-sm'))}" id="db-table">
						<thead>
							<tr>
								<th class="id-header" ng-if="multiEdit">
									<md-checkbox class="mb-0" ng-model="selectAll" ng-init="selectAll=false" ng-change="toggleSelectAll(selectAll)">
									</md-checkbox>
								</th>
								<th ng-repeat="(key, item) in headers" class="{{key==idName?'id-header':''}}">
									<a class="no-decor" href="" ng-click="sort(key)">
										{{ item['title'] }}
										<i class="fa {{ key==sortHeader ? ( isAscending ? 'fa-sort-asc' : 'fa-sort-desc' ) : 'fa-sort' }}"></i>
									</a>
								</th>
								<?php if ($permission >= PERMISSION_CHANGE): ?>
								<th ng-if="!multiEdit"></th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="(index, value) in data" ng-class="{'row-update' : (index==editIndex && isEdit), 'row-edit' : (index==editIndex)}">
								<td ng-if="multiEdit">
									<md-checkbox class="mb-0" ng-model="selection[index]">
									</md-checkbox>
								</td>
								<form no-validate>
									<td ng-repeat="(key, item) in headers">
										<span class="{{item.read_only?'':'cell-value '}}{{ key==sortHeader ? 'font-weight-bold' : '' }} cell-{{item.type}}">
											{{ item.prefix }}{{
											<?= $CI->load->view('item_formatter', 
											[ 
												'item_type' => 'item.type',
												'item_value' => 'value[key]'
											]
											, true); ?> 
											 }}{{ item.suffix }}
										</span>
										<?php if ($permission >= PERMISSION_CHANGE): ?>
										<?= $CI->load->view('input_switcher', 
											[ 
												'swtch' => 'item.type',
												'model' => 'value[key]',
												'cont_attr' => 'ng-if="!item.read_only"'
											]
										, true); ?>
										<?php endif ?>


									</td>
									<?php if ($permission >= PERMISSION_CHANGE): ?>
									<td ng-if="!multiEdit" class="toolbox">
										<md-button class="btn-edit md-square md-primary" ng-click="edit(index)"><i class="fa fa-pencil"></i></md-button>
										<md-button class="btn-edit md-square md-warn" ng-click="delete(index)"><i class="fa fa-trash"></i></md-button>
										<md-button class="btn-confirm md-square md-raised md-accent" ng-click="send()"><i class="fa fa-check"></i></md-button>
										<md-button class="btn-confirm md-square md-raised md-warn" ng-click="cancel()"><i class="fa fa-times"></i></md-button>
									</td>
									<?php endif ?>
								</form>
							</tr>
							<tr ng-if="data.length==0">
								<td colspan="{{ (headers | keyLength)
									<?= $permission >= PERMISSION_CHANGE?'+1':'' ?> }}">
									<div class="text-center" layout-padding>
										<div>
											<div ng-if="!filtered" style="padding-left: 15%; padding-right: 15%;}}">
												
												<i class="fa fa-minus-circle fa-3x pb-3" style="color: {{ colors('warn-400'); }}"></i>
												<h5 class="font-weight-bold">The Table is Empty</h5>

												<?php if ($permission >= PERMISSION_ADD): ?>
												<p>You might want to add an item by clicking on the (+) button located on the upper right of the page or this button here.</p>
												<md-button class="md-primary md-raised" ng-click="showDialog($event, 'addDialog', true)">
													Add an Item
												</md-button>
												<?php else: ?>
												<p>It looks like this table is yet to be filled up.</p>
												<?php endif ?>
											</div>
											<div ng-if="filtered" style="padding-left: 15%; padding-right: 15%;}}">
												
												<i class="fa fa-search fa-3x pb-3" style="color: {{ colors('primary-400'); }}"></i>
												<h5 class="font-weight-bold">No result found</h5>
												<p>The table did not have anything that matches your filter.</p>
											</div>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
			</div>
			<?php if ($permission >= PERMISSION_LOGIN): ?>
			<div style="visibility: hidden">
				<div class="md-dialog-container" id="filterNameDialog">
					<md-dialog>
						<md-toolbar>
							<div class="md-toolbar-tools">
								<h2>Save Filter</h2>
								<span flex></span>
								<md-button class="md-icon-button" ng-click="closeDialog()">
									<i class="fa fa-times fa-lg"></i>
								</md-button>
							</div>
						</md-toolbar>
						<form ng-cloak name="nameform" ng-submit="saveFilter(filterName)">
						<md-dialog-content>
							
								<div layout-padding>
									<md-input-container>
										<label>Name</label>
										<input ng-model="filterName">
									</md-input-container>
								</div>
							
						</md-dialog-content>
						<md-dialog-actions layout="row">
							<md-button ng-disabled="!nameform.$valid" type="submit" class="btn-confirm md-raised md-primary"><i class="fa fa-save"></i> Save</md-button>
						</md-dialog-actions>
						</form>
					</md-dialog>
				</div>
			</div>
			<div style="visibility: hidden">
				<div class="md-dialog-container" id="filterManager">
					<md-dialog>
						<md-toolbar>
							<div class="md-toolbar-tools">
								<h2>Manage Filters</h2>
								<span flex></span>
								<md-button class="md-icon-button" ng-click="closeDialog()">
									<i class="fa fa-times fa-lg"></i>
								</md-button>
							</div>
						</md-toolbar>
						<md-list>
							<md-list-item class="md-2-line align-items-center secondary-button-padding" ng-repeat-start="(id, filter) in userFilters">
								<p>{{ filter.query_title }}</p>
								<md-button class="md-secondary md-raised md-warn" ng-click="deleteFilter(id)">Delete</md-button>
							</md-list-item>
							<md-divider ng-repeat-end></md-divider>
						</md-list>
					</md-dialog>
				</div>
			</div>
			<?php endif ?>
			<?php if ($permission >= PERMISSION_ADD): ?>
			<div style="visibility: hidden">
				<div class="md-dialog-container" id="addDialog">
					<md-dialog>
						<md-toolbar>
							<div class="md-toolbar-tools">
								<h2>Add Item</h2>
								<span flex></span>
								<md-button class="md-icon-button" ng-click="closeDialog()">
									<i class="fa fa-times fa-lg"></i>
								</md-button>
							</div>
						</md-toolbar>
						<form ng-cloak name="addform" ng-submit="add(false)">
						<md-dialog-content>
								<div layout-padding>
									<div ng-repeat="(key, item) in headers" ng-if="!item.read_only" class="md-block">
										<?php echo $CI->load->view('input_switcher', 
											[
												'swtch' => 'item.type',
												'model' => 'newItem[key]',
												'label' => '{{item.title}}',
												'required' => 'item.required',
												'initialize' => 'item.default_value'
											]
										, true); ?>
									</div>
								</div>
						</md-dialog-content>
						<md-dialog-actions layout="row">
							<md-button ng-disabled="!addform.$valid" ng-click="add(true)" class="btn-confirm md-raised">Quick Add</md-button>
							<md-button ng-disabled="!addform.$valid" type="submit" class="btn-confirm md-raised md-primary"><i class="fa fa-plus"></i> Add</md-button>
						</md-dialog-actions>
						</form>
					</md-dialog>
				</div>
			</div>
			<?php endif ?>
		</md-card-content>
	</md-card>
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/database.js' ?>"></script>
<?php else: ?>
<div layout-padding class="text-center">
	<md-card class="pt-5">
		<i class="fa fa-user-secret fa-3x pb-3" style="color: {{ colors('primary-400'); }}"></i>
		<h3>This table is kept private</h3>
		<p>You will need to login to see this table.</p>
		<md-button class="md-raised md-primary" ng-href="<?=base_url()?>auth/login">
          <i class="fa fa-sign-in fa-fw fa-lg"> </i> Proceed to Login
        </md-button>
	</md-card>
</div>
<?php endif ?>
