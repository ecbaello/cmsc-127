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
			<md-grid-list class="text-center" md-cols="2" md-cols-sm="4" md-cols-md="6" md-cols-lg="8" md-cols-gt-lg="12" md-row-height="1:1" md-gutter="8px" md-gutter-gt-sm="4px">
				<md-grid-tile>
					<a class="link-grid-tile" href="<?=base_url().'database/detchar'?>">
						Patient Charges Detail
					</a>
				</md-grid-tile>
				<md-grid-tile>
					<a class="link-grid-tile" href="<?=base_url().'database/pcf'?>">
						Petty Cash Fund
					</a>
				</md-grid-tile>
				<md-grid-tile>
					<a class="link-grid-tile" href="<?=base_url().'database/pcfreport'?>">
						Petty Cash Fund Report
					</a>
				</md-grid-tile>
				<md-grid-tile>
					<a class="link-grid-tile" href="<?=base_url().'database/patientexp'?>">
						Patient Expenses
					</a>
				</md-grid-tile>
				<?php if (isset($extratables)): ?>
				<?php 	foreach($extratables as $value): ?>
				<md-grid-tile>
					<a class="link-grid-tile" href="<?=base_url().'database/custom/'.urlencode($value->table_name)?>">
						<?= $value->mdl_name ?>
					</a>
					
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