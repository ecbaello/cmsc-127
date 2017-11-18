<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<md-content layout-padding>
    <md-card>
        <md-card-title>
            <span class="md-headline">Expenses</span>
        </md-card-title>
        <md-card-content>
            <div ng-controller="reportTable" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
                
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
				
            </div>
        </md-card-content>
    </md-card>
</md-content>