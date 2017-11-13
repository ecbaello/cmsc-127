<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<md-content layout-padding>
    <md-card>
        <div ng-controller="LineCtrl" ng-init="setURL('<?= isset($url)?$url:current_url() ?>')">
            <canvas class="chart chart-line" chart-data="data" chart-labels="labels" chart-colors="colors"
                    chart-series="series" chart-options="options"
                    chart-click="onClick"></canvas>
        </div>

    </md-card>
</md-content>