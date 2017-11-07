<h1><?php echo lang('login_heading');?></h1>
<p><?php echo lang('login_subheading');?></p>

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
    <md-checkbox name="remember" value="0" required>
      Remember Me
    </md-checkbox>
  </div>
  <md-button type="submit" class="md-raised md-primary">Login</md-button>

<?php echo form_close();?>

<p><a href="forgot_password"><?php echo lang('login_forgot_password');?></a></p>