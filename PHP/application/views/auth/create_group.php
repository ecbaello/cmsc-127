<md-content layout-padding>
	<md-card>
		<md-card-title>
			<md-card-title-text>
				<span class="md-headline"><?php echo lang('create_group_heading');?></span>
				<span class="md-subhead"><?php echo lang('create_group_subheading');?></span>
			</md-card-title-text>
		</md-card-title>
		<md-card-content>

			<div id="infoMessage"><?php echo $message;?></div>

				<?php echo form_open("auth/create_group");?>

				<md-input-container>
					<?php echo lang('create_group_name_label', 'group_name');?>
					<?php echo form_input($group_name);?>
				</md-input-container>

				<md-input-container>
					<?php echo lang('create_group_desc_label', 'description');?>
					<?php echo form_input($description);?>
				</md-input-container>

				<div>
				<md-button class="md-raised md-primary" type="submit">
					<?= lang('create_group_submit_btn') ?>
				</md-button>
			</div>

		<?php echo form_close();?>

		</md-card-content>
	</md-card>
</md-content>