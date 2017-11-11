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