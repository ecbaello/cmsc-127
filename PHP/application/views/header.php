<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$CI->load->model('permission_model');

$selectNav = defined('NAV_SELECT') ? NAV_SELECT : -1;

?><!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
	<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Luke Foundation<?php isset($title)?' &gt; '.$title:'' ?></title>

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

  <link rel="shortcut icon" href="<?= base_url().'favicon.ico' ?>" type="image/x-icon">
  <link rel="icon" href="<?= base_url().'favicon.ico' ?>" type="image/x-icon">

  <link rel="stylesheet" href="<?= base_url().'css/roboto.css' ?>">
  <link rel="stylesheet" href="<?= base_url().'css/bootstrap.min.css' ?>">
  <link rel="stylesheet" href="<?= base_url().'css/angular-material.css' ?>">


  <link rel="stylesheet" href="<?= base_url().'css/font-awesome.css' ?>">
  <link rel="stylesheet" href="<?= base_url().'css/loader.css' ?>">
  <link rel="stylesheet" href="<?= base_url().'css/app.css' ?>">

  <script src="<?= base_url().'js/jquery.js' ?>"></script>

  <script src="<?= base_url().'js/bootstrap/popper.min.js' ?>"></script>
  <script src="<?= base_url().'js/bootstrap/bootstrap.min.js' ?>"></script>

  <script src="<?= base_url().'js/form-validator/form-validator.js' ?>"></script>
  <script src="<?= base_url().'js/stickytableheaders.js' ?>"></script>
  <script src="<?= base_url().'js/chart.min.js' ?>"></script>

  <script src="<?= base_url().'js/angular/angular.min.js' ?>"></script>
  <script src="<?= base_url().'js/angular/angular-animate.min.js' ?>"></script>
  <script src="<?= base_url().'js/angular/angular-messages.min.js' ?>"></script>
  <script src="<?= base_url().'js/angular/angular-aria.min.js' ?>"></script>
  <script src="<?= base_url().'js/angular/angular-material.min.js' ?>"></script>
  <script src="<?= base_url().'js/angular/angular-chart.min.js' ?>"></script>
  <script src="<?= base_url().'js/angular/ng-upload.min.js' ?>"></script>
  <script src="<?= base_url().'js/ng-table-to-csv.min.js' ?>"></script>
  <script src="<?= base_url().'js/init.js' ?>"></script>

</head>
<body ng-controller="navi" layout="column" class="">
  <div class="cloak-loader" ng-class="true?'hide':''">
    <div class="cloak-loader-wrapper" style="font-size: 1em">
      <div class="alt-loader-wrapper">
        <div class="alt-loader"></div>
      </div>
      Loading
    </div>
  </div>
  <div layout="row" flex="" ng-cloak>
    <md-sidenav class="md-sidenav-left main-sidenav" md-whiteframe="4" md-component-id="navigation" md-is-locked-open="$mdMedia('gt-md')">
      <div class="h-100" layout="column" md-theme="altTheme">
        <md-toolbar class="navbar logo">
          <h1 class="md-toolbar-tools"><img src="<?=base_url().'svg/luke_white.svg'?>"> Finance</h1>
        </md-toolbar>
        <md-content flex>
          <md-list class="main-menu fa-spacer" flex>
            <md-list-item class="<?= $selectNav==0 ? 'active' : '' ?>" href="<?=base_url() ?>">
              <i class="fa fa-home fa-lg fa-fw"></i> Home
            </md-list-item>
            <md-list-item class="<?= $selectNav==1 ? 'active' : '' ?>" href="<?=base_url().'database' ?>">
              <i class="fa fa-database fa-lg fa-fw"></i> Database
            </md-list-item>
            <?php if ($CI->permission_model->adminAllow()): ?>
            <md-list-item class="<?= $selectNav==2 ? 'active' : '' ?>" href="<?=base_url().'permissions' ?>">
              <i class="fa fa-group fa-lg fa-fw"></i> Permissions
            </md-list-item>
            <md-list-item class="<?= $selectNav==3 ? 'active' : '' ?>" href="<?=base_url().'tablemanager' ?>">
              <i class="fa fa-suitcase fa-lg fa-fw"></i> Table Manager
            </md-list-item>
            <?php endif ?>
            <md-list-item class="<?= $selectNav==4 ? 'active' : '' ?>" href="<?=base_url().'archives' ?>">
              <i class="fa fa-archive fa-lg fa-fw"></i> Archive
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
                <md-button class="md-icon-button" class="navbar-toggle" ng-click="toggleNavi()" hide-gt-md>
                    <span class="sr-only">Toggle navigation</span>
                    <span class="fa fa-navicon fa-2x"></span>
                </md-button>

                <span flex class="d-inline">
                </span>
                
                <div ng-controller="user">
                  <span class="user-menu" ng-if="loggedIn">
                    <md-menu>
                      <md-button ng-click="$mdMenu.open()">
                        <i class="fa fa-user-circle fa-fw fa-lg"> </i> {{ userTitle }}
                      </md-button>
                      <md-menu-content width="4">
                        <md-menu-item layout-padding>
                          Email: {{ email }}
                        </md-menu-item>
                        
                        <md-menu-item>
                          <md-button ng-href="<?=base_url()?>auth">
                            Manage Accounts
                          </md-button>
                        </md-menu-item>
                        <md-menu-item>
                          <md-button class="md-primary" ng-href="<?=base_url()?>auth/change_password">
                            Change Password
                          </md-button>
                        </md-menu-item>
                        <md-menu-item>
                          <md-button class="md-warn" ng-href="<?=base_url()?>auth/logout">
                            Logout
                          </md-button>
                        </md-menu-item>
                      </md-menu-content>
                    </md-menu>
                  </span>
                  <span class="user-out" ng-if="!loggedIn">
                    <md-button class="md-raised md-accent" ng-href="<?=base_url()?>auth/login">
                      <i class="fa fa-sign-in fa-fw fa-lg"> </i>Login
                    </md-button>
                  </span>
                </div>
            </div>
          </nav>
        </header>
      </md-toolbar>
      <div id="capsule">
