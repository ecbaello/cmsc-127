<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div id="container"  style="text-align: center;">
	<h4>Welcome to the Finance Database!</h4>

	<div id="body">
		
		<a class="button" href="<?=base_url().'detchar' ?>">Patient Charges Detail</a>
		<a class="button" href="<?=base_url().'pcf?t=General'?>">Petty Cash Fund</a>
		<a class="button" href="<?=base_url().'pcfrr'?>">Petty Cash Fund Replenishment Requests</a>
		<a class="button" href="<?=base_url().'patientexp'?>">Patient Expenses</a>
	</div>
</div>
