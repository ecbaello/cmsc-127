<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="<?= base_url().'css/foundation.css' ?>">
	<link rel="stylesheet" href="<?= base_url().'css/app.css' ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url().'css/motion-ui.css' ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url().'css/font-awesome.css' ?>">

  <title>Luke Foundation<?php isset($title)?' &gt; '.$title:'' ?></title>
  
	<script src="<?= base_url().'js/jquery.js' ?>"></script>
  <script src="<?= base_url().'js/form-validator.js' ?>"></script>
  <script src="<?= base_url().'js/tabledit.js' ?>"></script>
  <script src="<?= base_url().'js/stickytableheaders.js' ?>"></script>
</head>
<body>
<div class="off-canvas-wrapper" style="position: static;">
    <div class="off-canvas-wrapper-inner" data-off-canvas-wrapper>
      <div class="off-canvas position-left" id="offCanvasLeft" data-off-canvas>
        <ul class="navigation">
          <li>
            <a class="button alert" href="<?= base_url()?>search"><i class="fa fa-search"></i> Search</a>
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
      </div>
      <div class="off-canvas-content" data-off-canvas-content>
<div class="title-bar">
  <div class="title-bar-left">
    
    <button class="fa fa-bars" data-toggle="offCanvasLeft" style="font-size: 1rem; color: white; margin-right: 20px;"></button>
  	<a href="<?= base_url()?>">
    <h1 class="title-bar-title">Luke Foundation</h1>
    </a>
  </div>
</div>
<div id="capsule">