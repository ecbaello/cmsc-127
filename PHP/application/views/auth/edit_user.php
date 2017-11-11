<md-content layout-padding>
	<md-card>
		<md-card-title>
			<md-card-title-text>
				<span class="md-headline"><?php echo lang('edit_user_heading');?></span>
				<span class="md-subhead"><?php echo lang('edit_user_subheading');?></span>
			</md-card-title-text>
		</md-card-title>
		<md-card-content>

			<div id="infoMessage"><?php echo $message;?></div>

			<?php echo form_open(uri_string());?>

			<md-input-container>
				<?php echo lang('edit_user_fname_label', 'first_name');?>
				<?php echo form_input($first_name);?>
			</md-input-container>

			<md-input-container>
				<?php echo lang('edit_user_lname_label', 'last_name');?> 
				<?php echo form_input($last_name);?>
			</md-input-container>

			<md-input-container>
				<?php echo lang('edit_user_company_label', 'company');?>
				<?php echo form_input($company);?>
			</md-input-container>

			<md-input-container>
				<?php echo lang('edit_user_phone_label', 'phone');?>
				<?php echo form_input($phone);?>
			</md-input-container>

			<md-input-container>
				<?php echo lang('edit_user_password_label', 'password');?>
				<?php echo form_input($password);?>
			</md-input-container>

			<md-input-container>
				<?php echo lang('edit_user_password_confirm_label', 'password_confirm');?>
				<?php echo form_input($password_confirm);?>
			</md-input-container>

			<?php if ($this->ion_auth->is_admin()): ?>

			<h3><?php echo lang('edit_user_groups_heading');?></h3>
			<?php foreach ($groups as $group):?>
				<?php
					$gID=$group['id'];
					$checked = null;
					$item = null;
					foreach($currentGroups as $grp) {
						if ($gID == $grp->id) {
						$checked= ' ng-checked="true"';
						break;
						}
					}
				?>
				<md-checkbox type="checkbox" name="groups[]" value="<?php echo $group['id'];?>"<?php echo $checked;?>>
					<?php echo htmlspecialchars($group['name'],ENT_QUOTES,'UTF-8');?>
				</md-checkbox>
			<?php endforeach?>

			<?php endif ?>

			<?php echo form_hidden('id', $user->id);?>
			<?php echo form_hidden($csrf); ?>

			<div>
				<md-button class="md-raised md-primary" type="submit"><?= lang('edit_user_submit_btn') ?></md-button>
			</div>

			<?php echo form_close();?>

		</md-card-content>
	</md-card>
</md-content>
