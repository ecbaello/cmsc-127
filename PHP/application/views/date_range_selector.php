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

                <md-button class="md-primary" ng-init="makeTable('<?= $subtable ?>')" ng-click="makeTable('<?= $subtable ?>')">Go</md-button>
				<div id="container">
				<table class="table table-striped table-bordered table-hover">
					<tbody>
						<tr ng-repeat="data in table">
							<td ng-repeat="(key,value) in data">{{value}}</td>
						</tr>
					</tbody>
				</table>
				</div>
				
            </div>
        </md-card-content>
    </md-card>
</md-content>