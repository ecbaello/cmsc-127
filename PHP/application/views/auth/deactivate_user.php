<md-content layout-padding>
	<md-card>
		<md-card-title>
			<md-card-title-text>
				<span class="md-headline"><?php echo lang('deactivate_heading');?></span>
				<span class="md-subhead"><?php echo sprintf(lang('deactivate_subheading'), $user->username);?></span>
			</md-card-title-text>
		</md-card-title>
		<md-card-content>

			<?php echo form_open("auth/deactivate/".$user->id);?>

			<p>
				<?php echo lang('deactivate_confirm_y_label', 'confirm');?>
				<input type="radio" name="confirm" value="yes" checked="checked" />
				<?php echo lang('deactivate_confirm_n_label', 'confirm');?>
				<input type="radio" name="confirm" value="no" />
			</p>

			<?php echo form_hidden($csrf); ?>
			<?php echo form_hidden(array('id'=>$user->id)); ?>

			<div>
				<md-button class="md-raised md-primary" type="submit">
					<?= lang('deactivate_submit_btn') ?>
				</md-button>
			</div>

			<?php echo form_close();?>

		</md-card-content>
	</md-card>
</md-content>
