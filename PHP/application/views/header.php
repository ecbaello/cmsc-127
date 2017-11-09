<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

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

  <link rel="stylesheet" href="<?= base_url().'css/roboto.css' ?>">
  <link rel="stylesheet" href="<?= base_url().'css/bootstrap.min.css' ?>">
  <link rel="stylesheet" href="<?= base_url().'css/angular-material.css' ?>">


  <link rel="stylesheet" href="<?= base_url().'css/font-awesome.css' ?>">
  <link rel="stylesheet" href="<?= base_url().'css/loader.css' ?>">
  <link rel="stylesheet" href="<?= base_url().'css/app.css' ?>">

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
          <h2 class="md-toolbar-tools">Menu</h2>
        </md-toolbar>
        <md-content flex>
          <md-list class="main-menu" flex>
            <md-list-item class="active" href="<?=base_url() ?>">
              <i class="fa fa-dashboard"></i> Dashboard
            </md-list-item>
            <md-list-item href="<?=base_url().'database' ?>">
              <i class="fa fa-database"></i> Database
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
                    <span class="fa fa-navicon fa-lg"></span>
                </md-button>

                <h1 flex class="d-inline">
                  <a href="<?=base_url()?>">Luke Foundation, Inc. Database</a>
                </h1>
                
                <div ng-controller="user">
                  <span class="user-menu" ng-if="loggedIn">
                    <md-menu>
                      <md-button ng-click="$mdMenu.open()">
                        {{ userTitle }}
                      </md-button>
                      <md-menu-content width="4">
                        <md-menu-item layout-padding>
                          Email: {{ email }}
                        </md-menu-item>
                        <md-menu-item>
                          <md-button class="md-primary" ng-href="<?=base_url()?>auth">
                            Manage Account
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
                      Login
                    </md-button>
                  </span>
                </div>
            </div>
          </nav>
        </header>
      </md-toolbar>
      <div id="capsule">
