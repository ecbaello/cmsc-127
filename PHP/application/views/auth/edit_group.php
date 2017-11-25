<md-content layout-padding>
	<md-card>
		<md-card-title>
			<md-card-title-text>
				<span class="md-headline"><?php echo lang('edit_group_heading');?></span>
				<span class="md-subhead"><?php echo lang('edit_group_subheading');?></span>
			</md-card-title-text>
		</md-card-title>
		<md-card-content>

			<div id="infoMessage"><?php echo $message;?></div>

			<?php echo form_open(current_url());?>

			<md-input-container>
				<?php echo lang('edit_group_name_label', 'group_name');?> <br />
				<?php echo form_input($group_name);?>
			</md-input-container>

			<md-input-container>
				<?php echo lang('edit_group_desc_label', 'description');?> <br />
				<?php echo form_input($group_description);?>
			</md-input-container>

			<div>
				<md-button class="md-raised md-primary" type="submit">
					<?= lang('edit_group_submit_btn') ?>
				</md-button>
			</div>

			<?php echo form_close();?>

			<?php if ($group->id > 2): ?>
				<div class="mt-4">
					<md-button class="md-raised md-warn" href="<?php echo base_url().'auth/delete_group/'.$group->id ?>">
						<?= lang('delete_group_link_btn') ?>
					</md-button>
				</div>
			<?php endif ?>
		</md-card-content>
	</md-card>
</md-content>