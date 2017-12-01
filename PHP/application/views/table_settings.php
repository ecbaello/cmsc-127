<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
// $cont_attr?, $swtch, $model, $inp_attr?, $placeholder, $title
?>
<div ng-controller="tableSettings">
	<md-content layout-padding>
			<md-card class="p-0">
				<md-toolbar>
					<h5 class="md-toolbar-tools">Table Tools</h5>
				</md-toolbar>
				<md-tabs md-dynamic-height md-selected="0" md-align-tabs="top">
					<md-tab id="ts-tab1">
						<md-tab-label>General</md-tab-label>
						<md-tab-body>
							<div layout-padding>
								<div>
									<md-checkbox ng-init="checkPrivacy()" ng-model="isPrivate" ng-change="modifyPrivacy(isPrivate)">
										Make this table unavailable to the public
									</md-checkbox>
								</div>
								<div>
									<md-button class="md-primary md-raised" ng-click="download()">
										Download
									</md-button>
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
										<div class="col-lg-9 col-sm-7">
											<md-input-container class="w-100">
												<input placeholder="Title" type="text" ng-model="newColumn.title">
											</md-input-container>
										</div>
										<div class="col-lg-3 col-sm-5">
											<md-checkbox value="0" ng-model="newColumn.derived">
												Derived
											</md-checkbox>
										</div>
									</div>
									<div ng-if="!newColumn.derived">
										<md-checkbox class="md-warn" ng-model="newColumn.required">
												Required
										</md-checkbox>
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
						<md-tab-label>Edit Field</md-tab-label>
						<md-tab-body>
							<div layout-padding>
								<div>
									<md-button class="{{ item.derived ? 'md-accent' : 'md-primary' }} md-raised" ng-repeat="(key, item) in headers" ng-if="key!=idHeader" ng-click="editColumn(key)">
										<i class="fa fa-pencil">&nbsp;</i> 
										{{ item.title }}</i>
									</md-button>
								</div>
							</div>
						</md-tab-body>
					</md-tab>
				</md-tabs>
			</md-card>
	</md-content>
	<div style="visibility: hidden">
				<div class="md-dialog-container" id="renameColumnDialog">
					<md-dialog>
						<md-toolbar>
							<div class="md-toolbar-tools">
								<h2>Edit Field</h2>
								<span flex></span>
								<md-button class="md-icon-button" ng-click="closeDialog()">
									<i class="fa fa-times fa-lg"></i>
								</md-button>
							</div>
						</md-toolbar>
						<form ng-cloak name="renamecolumnform" ng-submit="renameColumn(editing, columnNewName)">
						<md-dialog-content>
								<div class="pt-3" layout-padding>
									<md-input-container class="w-100">
										<input placeholder="Name" type="text" ng-model="columnNewName" required>
									</md-input-container>
								</div>
						</md-dialog-content>
						<md-dialog-actions layout="row">
							<md-button type="submit" ng-disabled="!renamecolumnform.$valid" class="md-primary md-raised">
											Rename
							</md-button>
							<md-button class="{{ headers[editing].derived ? 'md-accent' : 'md-warn' }} md-raised" ng-click="removeColumn(editing)">
								Delete {{ headers[editing].title }}
							</md-button>
						</md-dialog-actions>
						</form>
					</md-dialog>
				</div>
			</div>
</div>
<script type="text/javascript" src="<?= base_url().'js/controllers/tablesettings.js' ?>"></script>
