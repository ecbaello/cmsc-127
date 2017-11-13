<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

?>
<script type="text/javascript">
	app.constant('permissionsURL', '<?= isset($url)?$url:current_url() ?>');
</script>
<div ng-controller="permissions">
	
</div>