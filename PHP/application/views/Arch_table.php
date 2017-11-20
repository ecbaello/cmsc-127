<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$CI->load->model('permission_model');
if (!isset($permission)) $permission = 0;
// $cont_attr?, $swtch, $model, $inp_attr?, $placeholder, $title
?>
<script type="text/javascript">
	app.constant('tableURL', '<?= isset($url)?$url:current_url() ?>');
</script>
<div ng-controller="database" ng-init="rebuild(true)">
	<md-content layout-padding>
		<md-card class="p-0">
			<md-progress-linear md-mode="indeterminate" ng-disabled="!serverRequesting"></md-progress-linear>
			<md-card-title>
				<span class="md-headline font-weight-bold" flex><?= $title ?></span>
				<span class="table-tools">
					<div>
                        <?php if(isset($subtable)): ?>
                            <md-button href="<?= base_url().'database/pcfreport/UnreplenishedPCF/'.urlencode($subtable) ?>" class="md-icon-button" >
                                <i class="fa fa-calculator fa-lg"></i>
                            </md-button>
                        <?php endif ?>
						<md-button ng-init="hideFilter=true" ng-class="hideFilter?'':' md-focused'" class="md-icon-button" ng-click="hideFilter=!hideFilter">
							<i class="fa fa-filter fa-lg"></i>
						</md-button>
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
								<md-button class="md-raised md-primary w-100" ng-disabled="filter.length==0" ng-click="rebuild(false)">Filter</md-button>
							</div>
						</div>
					</form>
					<div>
						<div>
							<md-select class="m-0" ng-model="currentUserFilterId" ng-change="filterChanged()" placeholder="Use saved filter" ng-init="loadFilters()">
								<md-option ng-repeat="(id, filter) in userFilters" ng-value="id" ng-selected="currentUserFilterId==id">
									<em>{{ filter.query_title }}</em>
								</md-option>
							</md-select>
							<md-button class="md-raised md-primary" ng-click="showFilterNameDialog()">Save...</md-button>
						</div>
					</div>
					<div class="row" layout-padding>
						<div class="col-lg-11 col-md-10 col-sm-8 text-right">Limit items by</div>
						<div class="col-lg-1 col-md-2 col-sm-4">
							<md-select class="m-0" ng-model="limit" ng-change="rebuild(false)" placeholder="Entries by">
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
						<span class="ml-3" ng-hide="!onJump">
							<strong> Jump to </strong>
							<md-input-container class="m-0 mini" style="width: 3em">
								<input type="number" ng-model="nextpage">
							</md-input-container>
						</span>
						<md-button class="md-icon-button {{onJump?'md-raised md-primary':''}}" ng-click="jump()">
							<i class="fa fa-bolt"></i>
						</md-button>
					</div>
					<div flex></div>
					<div class="text-right" layout-fill>
						<md-button class="md-accent md-raised md-icon-button" ng-click="navigate(false)">
							<i class="fa fa-chevron-left"></i>
						</md-button>
						<md-button class="md-accent md-raised md-icon-button" ng-click="navigate(true)">
							<i class="fa fa-chevron-right"></i>
						</md-button>
					</div>
				</div>
				<?php /** Table **/ ?>
				<div id="container">
					
						<table class="table table-striped table-bordered table-hover" id="db-table">
							<thead>
								<tr>
									<th ng-repeat="(key, item) in headers" class="{{key==idName?'id-header':''}}">
										<a class="no-decor" href="" ng-click="sort(key)">
											{{ item['title'] }}
											<i class="fa {{ key==sortHeader ? ( isAscending ? 'fa-sort-asc' : 'fa-sort-desc' ) : 'fa-sort' }}"></i>
										</a>
									</th>
									<?php if ($permission >= PERMISSION_CHANGE): ?>
									<th></th>
									<?php endif ?>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="(index, value) in data" ng-class="{'row-update' : (index==editIndex && isEdit), 'row-edit' : (index==editIndex)}">
									<form no-validate>
										<td ng-repeat="(key, item) in headers">
											<span ng-class="item.read_only?'':'cell-value'" class="{{ key==sortHeader ? 'font-weight-bold' : '' }}">
												{{ item.prefix }} {{
												<?= $CI->load->view('item_formatter', 
												[ 
													'item_type' => 'item.type',
													'item_value' => 'value[key]'
												]
												, true); ?> 
												 }} {{ item.suffix }}
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
										<td class="toolbox">
											<md-button class="btn-edit md-square md-primary" ng-click="edit(index)"><i class="fa fa-pencil"></i></md-button>
											<md-button class="btn-edit md-square md-warn" ng-click="delete(index)"><i class="fa fa-trash"></i></md-button>
											<md-button class="btn-confirm md-square md-raised md-accent" ng-click="send()"><i class="fa fa-check"></i></md-button>
											<md-button class="btn-confirm md-square md-raised md-warn" ng-click="cancel()"><i class="fa fa-times"></i></md-button>
										</td>
										<?php endif ?>
									</form>
								</tr>
							</tbody>
						</table>
						<div class="text-center" ng-if="data.length==0" layout-padding>
							<div>
								<i class="fa fa-minus-circle"></i> <em> Nothing to see here...</em>
							</div>
						</div>
				</div>
				<div style="visibility: hidden">
					<div class="md-dialog-container" id="filterNameDialog">
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
				<?php if ($permission >= PERMISSION_ADD): ?>
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
							<form ng-cloak name="addform" ng-submit="add()">
							<md-dialog-content>
								
									<div layout-padding>
										<div ng-repeat="(key, item) in headers" ng-if="!item.read_only" class="md-block">
											<?php echo $CI->load->view('input_switcher', 
												[
													'swtch' => 'item.type',
													'model' => 'newItem[key]',
													'label' => '{{item.title}}',
													'required' => 'item.required'
												]
											, true); ?>
											
										</div>
									</div>
								
							</md-dialog-content>
							<md-dialog-actions layout="row">
								<md-button ng-disabled="!addform.$valid" type="submit" class="btn-confirm md-raised md-primary"><i class="fa fa-save"></i> Save</md-button>
							</md-dialog-actions>
							</form>
						</md-dialog>
					</div>
				</div>
				<?php endif ?>
			</md-card-content>
		</md-card>
	</md-content>
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/database.js' ?>"></script>
