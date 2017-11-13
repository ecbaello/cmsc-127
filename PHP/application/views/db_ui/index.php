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
			<md-list>
				<md-list-item href="<?=base_url().'database/detchar' ?>">
					Patient Charges Detail
				</md-list-item>
				<md-list-item href="<?=base_url().'database/pcf'?>">
					Petty Cash Fund
				</md-list-item>
				<md-list-item href="<?=base_url().'database/pcfreport'?>">
					Petty Cash Fund Report
				</md-list-item>
				<md-list-item href="<?=base_url().'database/patientexp'?>">
					Patient Expenses
				</md-list-item>
				<?php if (isset($extratables)): ?>
					<?php foreach($extratables as $value): ?>
						<md-list-item href="<?=base_url().'database/custom/load/'.urlencode($value->table_name)?>">
							<?= $value->mdl_name ?>
						</md-list-item>
					<?php endforeach; ?>
				<?php endif ?>
			</md-list>
		</md-card-content>
	</md-card>
</md-content>
<?php 
	$CI->load->clear_vars();
?>