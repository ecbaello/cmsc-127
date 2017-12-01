<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
// $item_type, item_value
	$CI =& get_instance();
	$CI->load->model('permission_model');
	$permission = $CI->permission_model->adminAllow();
?>
<md-content layout-padding>
	<md-card>
		<md-card-title>
			<span class="md-headline">Database</span>
		</md-card-title>
		<style type="text/css">
			.link-grid-tile {
				font-weight: 500;
				background:rgba(224, 224, 224, 0.96);

			}
			.link-grid-tile:hover {
				background: {{ colors('primary-800'); }};
				color: white;
			}
		</style>
		<md-card-content>
			<md-grid-list class="text-center" md-cols="1" md-cols-sm="2" md-cols-md="4" md-cols-lg="5" md-cols-gt-lg="8" md-row-height="1:1" md-gutter="8px" md-gutter-gt-sm="4px">
				<md-grid-tile>
					<a class="link-grid-tile" href="<?=base_url().'database/report'?>">
						Financial Report
					</a>
				</md-grid-tile>
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
					<a class="link-grid-tile" href="<?=base_url().'database/patientexp'?>">
						Patient Expenses
					</a>
				</md-grid-tile>
				<?php if (isset($extratables)): ?>
				<?php 	foreach($extratables as $value): ?>
				<md-grid-tile>
					<a class="link-grid-tile" href="<?=base_url().'database/custom/load/'.urlencode($value->table_name)?>">
						<?= $value->mdl_name ?>
					</a>
					
				</md-grid-tile>
				<?php 	endforeach; ?>
				<?php endif ?>
			</md-grid-list>
			<?php if ($permission): ?>
				<p class="pt-3">You may want to recognize foreign tables in your database <a href="<?=base_url().'database/recognizer'?>">here</a>.</p>
			<?php endif ?>
		</md-card-content>
	</md-card>
</md-content>
<?php 
	$CI->load->clear_vars();
?>