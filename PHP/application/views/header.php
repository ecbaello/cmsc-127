<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
	<meta charset="utf-8">
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
<body>
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <a class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="fa fa-navicon fa-2x"></span>
        </a>
        <a class="navbar-brand" href="#"><img align = "left" class="img-responsive img-centered img-xs" style = "width:23px;" src="lukelogo.png"> Luke Foundation, Inc. Database</a>
    </div>
    <ul class="nav navbar-top-links navbar-right">
        <li>
          <a href="#">
              <span class="fa fa-home fa-fw fa-2x"></span>
          </a>
        </li>
        <li>
          <a href="#">
              <span class="fa fa-user-md fa-fw fa-2x"></span>
          </a>
        </li>
        <li>
          <a href="#">
              <span class="fa fa-user fa-fw fa-2x"></span>
          </a>
        </li>
    </ul>
</nav>
<ul class="navigation">
          <li>
            <a class="button tosearch" href="<?= base_url()?>search"><i class="fa fa-search"></i> Search</a>
          </li>
          <li>
            <a class="button" href="<?=base_url().'detchar' ?>">Patient Charges Detail</a>
          </li>
          <li>
            <a class="button" href="<?=base_url().'pcf'?>">Petty Cash Fund</a>
          </li>
          <li>
            <a class="button" href="<?=base_url().'pcfrr'?>">Petty Cash Fund Replenishment Requests</a>
          </li>
          <li>
            <a class="button" href="<?=base_url().'patientexp'?>">Patient Expenses</a>
          </li>
        </ul>
<div id="capsule">