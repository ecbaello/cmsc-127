<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<md-content layout-padding ng-controller="reportSettings" ng-init="setURL('<?= isset($url)?$url:current_url()?>','<?= isset($current_model)?$current_model:' ' ?>')">
	
	<?php if($this->permission_model->adminAllow()): ?>
	<md-content>
		<md-card>
			<md-toolbar style="background-color:green">
					<h5 class="md-toolbar-tools">Reporting Settings</h5>
			</md-toolbar>
			<md-tabs md-dynamic-height md-selected="0" md-align-tabs="top">
					<md-tab id="ts-tab1" md-on-select="changeTab()">
						<md-tab-label>Add Table To Reporting</md-tab-label>
						<md-tab-body>
							<div layout-padding>
								<md-select ng-change="selected()" placeholder="Table" ng-model="tableSelect">
									<md-option ng-value="key" ng-repeat="(key,value) in unTables">{{value.model}}</md-option>
								</md-select>
								<md-select ng-show="existsField" placeholder="Date Field" ng-model="fieldSelect">
									<md-option ng-value="k" ng-repeat="(k,v) in unTables[tableSelect].fields">{{v.title}}</md-option>
								</md-select>
								<p ng-if="!existsField && tableSelect!=='' " style="color:red">Table doesn't have a date field.</p>
								<md-button ng-click="addTable()" class="md-primary md-raised">Add</md-button>
							</div>
						</md-tab-body>
					</md-tab>
					<md-tab id="ts-tab2" md-on-select="changeTab()">
						<md-tab-label>Change Table Date Field</md-tab-label>
						<md-tab-body>
							<div layout-padding>
								<md-select placeholder="Table" ng-model="tableSelect">
									<md-option ng-value="key" ng-repeat="(key,value) in reportTables">{{value.model}}</md-option>
								</md-select>
								<md-select placeholder="Date Field" ng-model="fieldSelect">
									<md-option ng-value="k" ng-repeat="(k,v) in reportTables[tableSelect]['fields']">{{v.title}}</md-option>
								</md-select>
								<md-button ng-click="changeTable()" class="md-primary md-raised">Change</md-button>
							</div>
						</md-tab-body>
					</md-tab>
					<md-tab id="ts-tab3" md-on-select="changeTab()">
						<md-tab-label>Remove Table From Reporting</md-tab-label>
						<md-tab-body>
							<div layout-padding>
								<md-select placeholder="Table" ng-model="tableSelect">
									<md-option ng-value="key" ng-repeat="(key,value) in reportTables">{{value.model}}</md-option>
								</md-select>
								<md-button ng-click="removeTable()" class="md-primary md-raised">Remove</md-button>
							</div>
						</md-tab-body>
					</md-tab>
				</md-tabs>
		</md-card>
	</md-content>
	<?php endif ?>
	
</md-content>