<md-content layout-padding>
	<md-card>
		<md-card-title>
			<md-card-title-text>
				<span class="md-headline"><?php echo lang('change_password_heading');?></span>
			</md-card-title-text>
		</md-card-title>
		<md-card-content>

		<div id="infoMessage"><?php echo $message;?></div>

			<?php echo form_open("auth/change_password");?>

			<md-input-container>
				<?php echo lang('change_password_old_password_label', 'old_password');?>
				<?php echo form_input($old_password);?>
			</md-input-container>

			<md-input-container>
				<label for="new_password"><?php echo sprintf(lang('change_password_new_password_label'), $min_password_length);?></label>
				<?php echo form_input($new_password);?>
			</md-input-container>

			<md-input-container>
				<?php echo lang('change_password_new_password_confirm_label', 'new_password_confirm');?>
				<?php echo form_input($new_password_confirm);?>
			</md-input-container>

			<?php echo form_input($user_id);?>

		<div>
			<md-button class="md-raised md-primary" type="submit">
				<?= lang('change_password_submit_btn') ?>
			</md-button>
		</div>

		<?php echo form_close();?>

		</md-card-content>
	</md-card>
</md-content>