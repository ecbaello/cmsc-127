<md-content layout-padding>
	<md-card>
		<md-card-title>
			<md-card-title-text>
				<span class="md-headline"><?php echo lang('create_user_heading');?></span>
				<span class="md-subhead"><?php echo lang('create_user_subheading');?></span>
			</md-card-title-text>
		</md-card-title>
		<md-card-content>

			<div id="infoMessage"><?php echo $message;?></div>

			<?php echo form_open("auth/create_user");?>

			<md-input-container>
				<?php echo lang('create_user_fname_label', 'first_name');?> 
				<?php echo form_input($first_name);?>
			</md-input-container>

			<md-input-container>
				<?php echo lang('create_user_lname_label', 'last_name');?> 
				<?php echo form_input($last_name);?>
			</md-input-container>

			<?php
				if($identity_column!=='email') {
				echo '<md-input-container>';
				echo lang('create_user_identity_label', 'identity');
				echo form_error('identity');
				echo form_input($identity);
				echo '</md-input-container>';
				}
			?>

			<md-input-container>
				<?php echo lang('create_user_company_label', 'company');?> 
				<?php echo form_input($company);?>
			</md-input-container>

			<md-input-container>
				<?php echo lang('create_user_email_label', 'email');?> 
				<?php echo form_input($email);?>
			</md-input-container>

			<md-input-container>
				<?php echo lang('create_user_phone_label', 'phone');?> 
				<?php echo form_input($phone);?>
			</md-input-container>

			<md-input-container>
				<?php echo lang('create_user_password_label', 'password');?> 
				<?php echo form_input($password);?>
			</md-input-container>

			<md-input-container>
				<?php echo lang('create_user_password_confirm_label', 'password_confirm');?> 
				<?php echo form_input($password_confirm);?>
			</md-input-container>


			<div>
				<md-button class="md-raised md-primary" type="submit">
					<?= lang('create_user_submit_btn') ?>
				</md-button>
			</div>

			<?php echo form_close();?>

		</md-card-content>
	</md-card>
</md-content>
