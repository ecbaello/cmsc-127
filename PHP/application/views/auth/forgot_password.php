<md-content layout-padding>
	<md-card>
		<md-card-title>
			<md-card-title-text>
				<span class="md-headline"><?php echo lang('forgot_password_heading');?></span>
				<span class="md-subhead"><?php echo sprintf(lang('forgot_password_subheading'), $identity_label);?></span>
			</md-card-title-text>
		</md-card-title>
		<md-card-content>

			<div id="infoMessage"><?php echo $message;?></div>

			<?php echo form_open("auth/forgot_password");?>

			<md-input-container>
				<label for="identity"><?php echo (($type=='email') ? sprintf(lang('forgot_password_email_label'), $identity_label) : sprintf(lang('forgot_password_identity_label'), $identity_label));?></label>
				<?php echo form_input($identity);?>
			</md-input-container>

			<div>
				<md-button class="md-raised md-primary" type="submit">
					<?= lang('forgot_password_submit_btn') ?>
				</md-button>
			</div>

			<?php echo form_close();?>

		</md-card-content>
	</md-card>
</md-content>