<md-content layout-padding>
	<md-card>
		<md-card-title>
			<md-card-title-text>
				<span class="md-headline"><?php echo lang('login_heading');?></span>
				<span class="md-subhead"><?php echo lang('login_subheading');?></span>
			</md-card-title-text>
		</md-card-title>
		<md-card-content>

			<div id="infoMessage"><?php echo $message;?></div>

			<?php echo form_open("auth/login", [ 'enctype'=>'application/json']);?>

			<md-input-container>
				<label>Email</label>
				<?php echo form_input($identity);?>
			</md-input-container>

			<md-input-container>
				<label>Password</label>
				<?php echo form_input($password);?>
			</md-input-container>

			<div>
				<md-button type="submit" class="md-raised md-primary">Login</md-button>
				<md-checkbox class="ml-3" name="remember" value="0" required>
				Remember Me
				</md-checkbox>
			</div>


			<?php echo form_close();?>
			<br>

			<p><a href="forgot_password"><?php echo lang('login_forgot_password');?></a></p>
		</md-card-content>
	</md-card>
</md-content>