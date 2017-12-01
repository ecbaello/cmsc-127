<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<md-content layout-padding>
    <md-card>
        <md-card-title>
            <span class="md-headline">Select Range</span>
        </md-card-title>
        <md-card-content>
            <div ng-controller="dateRangeSelector" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
                <md-datepicker md-max-date="toDate" ng-required="TRUE" md-placeholder="From" ng-model="fromDate"></md-datepicker>
                <md-datepicker md-min-date="fromDate" ng-required="TRUE" md-placeholder="To" ng-model="toDate"></md-datepicker>

                <md-button class="md-primary md-raised" ng-init="makeTable('<?= $subtable ?>')" ng-click="makeTable('<?= $subtable ?>')">Go</md-button>
				<md-button class="md-icon-button md-raised" title="Export Table" ng-click='csvCustom.generate()' ng-href="{{ csvCustom.link() }}" download="fin_custom_report.csv">
					<i class="fa fa-download fa-lg"></i>
				</md-button>
				<div id="container">

				<table class="table table-striped table-bordered table-hover" export-csv="csvCustom">
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
				
            </div>
        </md-card-content>
    </md-card>
</md-content>