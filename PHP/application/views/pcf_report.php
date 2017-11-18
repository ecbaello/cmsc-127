<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<md-content ng-controller="pcfReport" layout-padding>
    <md-card>
        <md-card-content>
            <div ng-init="setURL('<?= isset($url)?$url:current_url() ?>','<?= isset($subtable) ? $subtable:' ' ?>')">
                <span class="md-headline">Unreplenished Petty Cash <?= isset($subtable) ? 'For '.urldecode($subtable).' Fund':'' ?> </span>
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

                <div>
                    <p ng-repeat="(key, value) in details">{{key}}: {{value.data}}</p>

                </div>

            </div>
        </md-card-content>
    </md-card>

    <?php if($this->permission_model->adminAllow()): ?>
    <md-card>
        <md-card-content>Change Data:

            <p>Allotted Fund: <input type="text" ng-model="inFund"/>
                <md-button ng-click="changeFund('<?= isset($subtable) ? $subtable:' ' ?>')" class="md-primary">Change</md-button></p>
            <p>Expense Threshold: <input type="text" ng-model="inThreshold"/>
                <md-button ng-click="changeThreshold('<?= isset($subtable) ? $subtable:' ' ?>')" class="md-primary">Change</md-button></p>
            <p><md-button class="md-primary" ng-click="replenish('<?= isset($subtable) ? $subtable:' ' ?>')">Replenish</md-button></p>

        </md-card-content>
    </md-card>
    <?php endif ?>
</md-content>