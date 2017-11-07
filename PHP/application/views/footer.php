<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$CI->load->library('ion_auth');
$user = $CI->ion_auth->user()->row();
$logged_in = $CI->ion_auth->logged_in();
?>
</div>


<div style="text-align: center; font-size: 0.7em; margin-top: 2em;">&copy; Copyright 2017. All Rights Reserved.</div>
		</md-content>
    </div>
<script src="<?= base_url().'js/jquery.js' ?>"></script>

<script src="<?= base_url().'js/popper.min.js' ?>"></script>
<script src="<?= base_url().'js/bootstrap.min.js' ?>"></script>

<script src="<?= base_url().'js/form-validator.js' ?>"></script>
<script src="<?= base_url().'js/stickytableheaders.js' ?>"></script>

<script src="<?= base_url().'js/angular.min.js' ?>"></script>
<script src="<?= base_url().'js/angular-animate.min.js' ?>"></script>
<script src="<?= base_url().'js/angular-messages.min.js' ?>"></script>
<script src="<?= base_url().'js/angular-aria.min.js' ?>"></script>
<script src="<?= base_url().'js/angular-material.min.js' ?>"></script>

<script src="<?= base_url().'js/init.js' ?>"></script>

<script type="text/javascript">
	app.factory('UserService', [function() {
	  return {
	    isLogged: <?= 	$logged_in?"true":"false" ?>,
	    firstName: <?= 	$logged_in?"'".$user->first_name."'":"null" ?>,
	    lastName: <?= 	$logged_in?"'".$user->last_name."'":"null" ?>,
	    company: <?= 	$logged_in?"'".$user->company."'":"null" ?>,
	    email: <?= 		$logged_in?"'".$user->email."'":"null" ?>
	  };
	}]);
</script>

<script src="<?= base_url().'js/app.js' ?>"></script>

</body>
</html>