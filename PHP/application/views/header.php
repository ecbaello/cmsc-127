<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
	<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Luke Foundation<?php isset($title)?' &gt; '.$title:'' ?></title>
  
	<script src="<?= base_url().'js/jquery.js' ?>"></script>

  <script src="<?= base_url().'js/popper.js' ?>"></script>
  <script src="<?= base_url().'js/bootstrap.js' ?>"></script>

  <script src="<?= base_url().'js/form-validator.js' ?>"></script>
  <script src="<?= base_url().'js/stickytableheaders.js' ?>"></script>

  <script src="<?= base_url().'js/angular.js' ?>"></script>
  <script src="<?= base_url().'js/angular-animate.js' ?>"></script>
  <script src="<?= base_url().'js/angular-messages.js' ?>"></script>
  <script src="<?= base_url().'js/angular-aria.js' ?>"></script>
  <script src="<?= base_url().'js/angular-material.js' ?>"></script>

  <style type="text/css">
    input, textarea {
    border: none;
    overflow: auto;
    outline: none;

    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
}
  </style>


  <link rel="stylesheet" href="<?= base_url().'css/bootstrap.min.css' ?>">
  <link rel="stylesheet" href="<?= base_url().'css/angular-material.css' ?>">

  <link rel="stylesheet" href="<?= base_url().'css/font-awesome.css' ?>">
  <link rel="stylesheet" href="<?= base_url().'css/app.css' ?>">
</head>
<body ng-controller="navi" layout="column">
  <div layout="row" flex="">
    <md-sidenav class="md-sidenav-left main-sidenav" md-whiteframe="4" md-component-id="navigation" md-is-locked-open="$mdMedia('gt-md')">
      <div class="h-100" layout="column" md-theme="altTheme">
        <md-toolbar class="navbar">
          <h2 class="md-toolbar-tools">Menu</h2>
        </md-toolbar>
        <md-content flex>
          <md-list flex>
            <md-list-item href="<?= base_url()?>search">
              Search
            </md-list-item>
            <md-list-item href="<?=base_url().'detchar' ?>">
              Patient Charges Detail
            </md-list-item>
            <md-list-item href="<?=base_url().'pcf'?>">
              Petty Cash Fund
            </md-list-item>
            <md-list-item href="<?=base_url().'pcfrr'?>">
              Petty Cash Fund Replenishment Requests
            </md-list-item>
            <md-list-item href="<?=base_url().'patientexp'?>">
              Patient Expenses
            </md-list-item>
          </md-list>
        </md-content>
      </div>
    </md-sidenav>
    <md-content flex="">
      <md-toolbar>
        <header>
          <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="md-toolbar-tools">
                <md-button class="md-icon-button" class="navbar-toggle" ng-click="toggleNavi()">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="fa fa-navicon fa-lg"></span>
                </md-button>
                <h1 class="d-inline">
                  <a href="<?=base_url()?>">Luke Foundation, Inc. Database</a>
                </h1>
            </div>
          </nav>
        </header>
      </md-toolbar>
      <div id="capsule">
