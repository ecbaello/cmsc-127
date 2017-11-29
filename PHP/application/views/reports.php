<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<md-content layout-padding ng-controller="reportTable" ng-init="setURL('<?= isset($url)?$url:current_url()?>','<?= isset($current_model)?$current_model:' ' ?>')">
	
	<md-card>
		<md-card-title>
			<span class="md-headline">Detailed Expenses</span>
		</md-card-title>
		
		<md-card-content>
			<md-button class="md-icon-button md-raised" title="Export Table" ng-click='csvReport.generate()' ng-href="{{ csvReport.link() }}" download="fin_report.csv">
						<i class="fa fa-download fa-lg"></i>
					</md-button>
					
			<div id="container">
				<table class="table table-striped table-bordered table-hover" export-csv="csvReport">
					<thead>
						<tr>
							<th ng-repeat="value in table[0]">{{value}}</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="data in table" ng-if="!$first">
							<td ng-repeat="value in data track by $index">{{value}}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</md-card-content>
	</md-card>
	
	<md-card>
        <md-card-title>
            <span class="md-headline">Select Range</span>
        </md-card-title>
        <md-card-content>
            <div>
                <md-datepicker md-max-date="toDate" ng-required="TRUE" md-placeholder="From" ng-model="fromDate"></md-datepicker>
                <md-datepicker md-min-date="fromDate" ng-required="TRUE" md-placeholder="To" ng-model="toDate"></md-datepicker>

                <md-button class="md-primary md-raised" ng-click="makeCustomTable()">Go</md-button>
				<md-button class="md-icon-button md-raised" title="Export Table" ng-click='csvCustom.generate()' ng-href="{{ csvCustom.link() }}" download="fin_custom_report.csv">
					<i class="fa fa-download fa-lg"></i>
				</md-button>
				<div  style="overflow-x:auto">

				<table class="table table-striped table-bordered table-hover" export-csv="csvCustom">
					<thead>
						<tr>
							<th ng-repeat="(key, value) in customTable[0]">{{value}}</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="data in customTable" ng-if="!$first">
							<td ng-repeat="(key,value) in data">{{value}}</td>
						</tr>
					</tbody>
				</table>
				</div>
				
            </div>
        </md-card-content>
    </md-card>
	<?php if($this->permission_model->adminAllow()): ?>
	<md-content>
		<md-card>
			<md-toolbar style="background-color:green">
					<h5 class="md-toolbar-tools">Fields Settings</h5>
			</md-toolbar>
			<md-card-content>
				<div>
					<div>
						<div  style="overflow-x:auto">
						<table class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th>Fields</th>
									<th>Options</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="(key,field) in fields">
									<td>{{field.name}}</td>
									<td>
									<md-radio-group layout='row' ng-model="fields[key].option">
										<md-radio-button value=1>
											Additive
										</md-radio-button>
										<md-radio-button value=2>
											Subtractive
										</md-radio-button>
										<md-radio-button value=0>
											Not Included
										</md-radio-button>
									</md-radio-group>
									</td>
								</tr>							
							</tbody>
						</table>
						<md-button ng-click="changeFields()" class="md-primary md-raised" >
							Change Fields Settings
						</md-button>
						</div>
					</div>
					
					
				</div>
			</md-card-content>
			
		</md-card>
	</md-content>
	<?php endif ?>
	
</md-content>