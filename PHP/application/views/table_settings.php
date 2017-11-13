<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
// $cont_attr?, $swtch, $model, $inp_attr?, $placeholder, $title
?>
<div ng-controller="tableSettings" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
	<md-content layout-padding>
			<md-card class="p-0">
				<md-toolbar>
					<h5 class="md-toolbar-tools">Settings</h5>
				</md-toolbar>
				<md-tabs md-dynamic-height md-selected="0" md-align-tabs="top">
					<md-tab id="ts-tab1">
						<md-tab-label>General</md-tab-label>
						<md-tab-body>
							<div layout-padding>
								<div>
								</div>
							</div>
						</md-tab-body>
					</md-tab>
					<md-tab id="ts-tab2">
						<md-tab-label>Add Field</md-tab-label>
						<md-tab-body>
							<div layout-padding>
								<div>
									<div class="row">
										<div class="col-lg-10 col-sm-9">
											<md-input-container class="w-100">
												<input placeholder="Title" type="text" ng-model="newColumn.title">
											</md-input-container>
										</div>
										<div class="col-lg-2 col-sm-3">
											<md-checkbox value="0" ng-model="newColumn.derived">
												Derived
											</md-checkbox>
										</div>
									</div>
									<div ng-if="!newColumn.derived">
										<md-select placeholder="Field Type" ng-model="newColumn.kind">
											<md-option ng-repeat="(key, item) in types" ng-value="key">
												{{ item }}
											</md-option>
										</md-select>
										<div class="w-100">
											<?php echo $CI->load->view('input_switcher', 
												[ 
													'swtch' => 'newColumn.kind',
													'model' => 'newColumn.default',
													'label' => 'Default Value',
													'cont_attr' => 'class="w-100"'
												]
											, true); ?>
										</div>
									</div>
									<div ng-if="newColumn.derived">
										<label>Field Derivation</label>
										<md-chips ng-model="derivedColumnExpr" md-transform-chip="transformChip($chip)">
											<md-chip-template>
												<span ng-switch="$chip.type">
													<span ng-switch-when="field">
														{{ $chip.title }} <em>(field)</em>
													</span>
													<span ng-switch-when="operation">
														{{ $chip.value }}
													</span>
												</span>
											</md-chip-template>
											<input type="text" placeholder="Add to Expression">
										</md-chips>
										<div class="row" layout-padding>
											<div class="col-lg-9 col-sm-6">
												<md-select class="md-no-underline" placeholder="Select a Field" ng-model="newDToken.header">
													<md-option ng-repeat="(key, item) in headers" ng-value="key">
														{{ item.title }}
													</md-option>
												</md-select>
											</div>
											<div class="col-lg-3 col-sm-6 text-right">
												<md-button class="md-raised" ng-click="addDToken()">
													<i class="fa fa-plus fa-lg"></i> Add Field
												</md-button>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-6">
											<md-input-container class="w-100">
												<input placeholder="Prefix" type="text" ng-model="newColumn.prefix">
											</md-input-container>
										</div>
										<div class="col-6">
											<md-input-container class="w-100">
												<input placeholder="Suffix" type="text" ng-model="newColumn.suffix">
											</md-input-container>
										</div>
									</div>
									<div class="text-right">
										<md-button class="md-raised md-primary" ng-click="addColumn()">
											<i class="fa fa-plus fa-lg"></i> Add Column
										</md-button>
									</div>
								</div>
							</div>
						</md-tab-body>
					</md-tab>
					<md-tab id="ts-tab3">
						<md-tab-label>Remove Field</md-tab-label>
						<md-tab-body>
							<div layout-padding>
								<div>
									<md-button class="{{ item.derived ? 'md-accent' : 'md-warn' }} md-raised" ng-repeat="(key, item) in headers" ng-if="key!=idHeader" ng-click="removeColumn(key)">
										{{ item.title }}&nbsp; <i class="fa fa-times"></i>
									</md-button>
								</div>
							</div>
						</md-tab-body>
					</md-tab>
				</md-tabs>
			</md-card>
	</md-content>
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/tablesettings.js' ?>"></script>
