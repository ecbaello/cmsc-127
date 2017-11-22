<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
// $item_type, item_value
	$CI =& get_instance();
?>
<md-content layout-padding>
	<md-card>
		<md-card-title>
			<span class="md-headline">Database</span>
		</md-card-title>
		<md-card-content>
			<md-grid-list md-cols-sm="2" md-cols-md="4" md-cols-lg="8" md-cols-gt-lg="12" md-row-height="1:1">
				<md-grid-tile>
					Patient Charges Detail
				</md-grid-tile>
				<md-grid-tile href="<?=base_url().'database/pcf'?>">
					Petty Cash Fund
				</md-grid-tile>
				<md-grid-tile href="<?=base_url().'database/pcfreport'?>">
					Petty Cash Fund Report
				</md-grid-tile>
				<md-grid-tile href="<?=base_url().'database/patientexp'?>">
					Patient Expenses
				</md-grid-tile>
				<?php if (isset($extratables)): ?>
				<?php 	foreach($extratables as $value): ?>
				<md-grid-tile href="<?=base_url().'database/custom/load/'.urlencode($value->table_name)?>">
					<?= $value->mdl_name ?>
				</md-grid-tile>
				<?php 	endforeach; ?>
				<?php endif ?>
			</md-grid-list>
		</md-card-content>
	</md-card>
</md-content>
<?php 
	$CI->load->clear_vars();
?>