<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<md-content ng-controller="pcfReport" ng-init="setURL('<?= isset($url)?$url:current_url() ?>','<?= isset($subtable) ? $subtable:' ' ?>')" layout-padding>
	
	<md-content>
		<md-card>
			<md-toolbar style="background-color:green">
				<h5 class="md-toolbar-tools">Unreplenished Petty Cash <?= isset($subtable) ? 'For '.urldecode($subtable).' Fund':'' ?> </h5>
				
			</md-toolbar>
			<md-card-content>
				<md-button class="md-icon-button md-raised" title="Export Table" ng-click='csvqr.generate()' ng-href="{{ csvqr.link() }}" download="pcf_report.csv">
					<i class="fa fa-download fa-lg"></i>
				</md-button>
				<div id="container">

					<table class="table table-striped table-bordered table-hover" export-csv="csvqr">
						<thead>
							<tr>
								<th ng-repeat="(key, value) in table[0]">{{value}}</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="data in table" ng-if="!$first">
								<td ng-repeat="(key,value) in data">{{value}}</td>
							</tr>
						</tbody>
					</table>

					</div>
			</md-card-content>
		</md-card>
	</md-content>
	
	<md-content>
		<md-card>
			<md-toolbar ng-style="{backgroundColor: details['Expense Total'] > details['Expense Threshold'] ? 'darkred' : 'green'}">
				<h5 class="md-toolbar-tools">Details</h5>
			</md-toolbar>
			<md-card-content>
				<md-content>
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th ng-repeat="(key,value) in details">{{key}}</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td  ng-repeat="value in details">{{value}}</td>
							</tr>
						</tbody>
					</table>
				</md-content>
			</md-card-content>
		</md-card>
	</md-content>
	
    <?php if($this->permission_model->adminAllow()): ?>
    <md-content>
		<md-card>
			<md-toolbar style="background-color:green">
				<h5 class="md-toolbar-tools">Change Data</h5>
			</md-toolbar>
			<md-card-content>

				<md-input-container class="w-100">
					<input placeholder="Alotted Fund" type="text" ng-model="inFund"/>
				</md-input-container>
					
				<md-input-container class="w-100">
					<input placeholder="Expense Threshold" type="text" ng-model="inThreshold"/>
					<section layout="row" layout-align="end center">
						<md-button ng-click="changeFund('<?= isset($subtable) ? $subtable:' ' ?>'); changeThreshold('<?= isset($subtable) ? $subtable:' ' ?>')" class="md-primary md-raised">Change</md-button>
					</section>
				</md-input-container>
				<md-button class="md-primary md-raised" ng-href="{{ csvqr.link() }}" download="pcf_replenishment_report.csv" ng-click="csvqr.generate(); replenish('<?= isset($subtable) ? $subtable:' ' ?>')">Replenish Funds</md-button>

			</md-card-content>
		</md-card>
	</md-content>
    <?php endif ?>
</md-content>