<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>
<md-content layout-padding>
    <md-card>
        <md-card-content>
            <div ng-controller="pcfReport" ng-init="setURL('<?= isset($url)?$url:current_url() ?>','<?= isset($subtable) ? $subtable:' ' ?>')">
                <span>Unreplenished Petty Cash <?= isset($subtable) ? 'For '.urldecode($subtable).' Fund':'' ?> </span>
                <span class="table-tools"><a class="btn" title="Export Table" ng-click='csvqr.generate()' ng-href="{{ csvqr.link() }}" download="pcf_report.csv">
                    <i class="fa fa-download fa-lg"></i>
                </a></span>
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

            </div>
        </md-card-content>
    </md-card>
</md-content>