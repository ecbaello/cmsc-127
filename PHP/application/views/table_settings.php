<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
// $cont_attr?, $swtch, $model, $inp_attr?, $placeholder, $title
?>
<div ng-controller="tableSettings" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
	<md-content layout-padding>
	  	<md-card>
	    	<md-card-title>
	    		<span class="md-headline" flex>Table Settings</span>
	    	</md-card-title>
	    	<md-card-content>
	    		<md-input-container class="w-100">
					<input placeholder="Title" type="text" ng-model="newColumn.title">
				</md-input-container>
				<div>
					<md-checkbox value="0" ng-model="newColumn.derived">
						Derived
					</md-checkbox>
				</div>
				<div ng-if="!newColumn.derived">
					<md-select placeholder="Field Type" ng-model="newColumn.kind">
						<md-option ng-repeat="(key, item) in types" ng-value="key">
							{{ item }}
						</md-option>
					</md-select>
					<md-input-container class="w-100">
						<input placeholder="Default Value" type="text" ng-model="newColumn.default">
					</md-input-container>
				</div>
	    		<div ng-if="newColumn.derived">
	    			<label>Field Derivation</label>
					<md-chips ng-model="derivedColumnExpr">
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
						<input type="hidden" disabled>
					</md-chips>
					<div class="row">
						<div class="col-lg-2 col-sm-4">
							<md-switch ng-model="DTokenIsField">
								Field
							</md-switch>
						</div>
						<div class="col-lg-10 col-sm-8">
							<md-input-container class="w-100" ng-if="!DTokenIsField">
								<input placeholder="Operation" type="text" ng-model="newDToken.value">
							</md-input-container>
							<md-select placeholder="Field" ng-model="newDToken.header" ng-if="DTokenIsField">
								<md-option ng-repeat="(key, item) in headers" ng-value="key">
									{{ item.title }}
								</md-option>
							</md-select>
						</div>
					</div>
					<md-button class="md-raised" ng-click="addDToken(DTokenIsField)">
						<i class="fa fa-plus fa-lg"></i> Add to Derivation
					</md-button>
				</div>
				<md-button class="md-raised md-primary" ng-click="addColumn()">
						<i class="fa fa-plus fa-lg"></i> Add Column
					</md-button>
	     	</md-card-content>
	    </md-card>
	</md-content>
</div>
