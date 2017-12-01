<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<md-content layout-padding>
    <md-card>
        <div ng-controller="LineCtrl" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
			<div>
			<hr>
			Year: <input type="number" ng-model="year" placeholder="Year" ng-change="setYear()" ng-pattern='/^[0-9]{4}$/' required></input>
			<hr>
			</div>
            <canvas class="chart chart-line" chart-data="data" chart-labels="labels"
                    chart-series="series" chart-options="options"
                    chart-click="onClick"></canvas>
        </div>

    </md-card>
</md-content>