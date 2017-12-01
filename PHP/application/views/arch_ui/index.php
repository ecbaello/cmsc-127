<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
// $item_type, item_value
	$CI =& get_instance();
?>
<md-content layout-padding>
	<md-card>
		<md-card-title>
			<span class="md-headline">Archives</span>
		</md-card-title>
		<md-card-content>
			<md-list>
				<md-list-item href="<?=base_url().'archives/detchar' ?>">
					Patient Charges Detail
				</md-list-item>
				<md-list-item href="<?=base_url().'archives/patientexp'?>">
					Patient Expenses
				</md-list-item>
			</md-list>
		</md-card-content>
	</md-card>
</md-content>
<?php 
	$CI->load->clear_vars();
?>