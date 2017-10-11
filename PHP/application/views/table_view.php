<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>
	<link rel="stylesheet" href="<?= FOUNDATION ?>css/foundation.css">
</head>
<body>

<div id="container">
	<?php 
		echo $tablehtml	
		?>
</div>

 <script src="<?= FOUNDATION ?>js/vendor/jquery.js"></script>
 <script src="<?= FOUNDATION ?>js/vendor/what-input.js"></script>
 <script src="<?= FOUNDATION ?>js/vendor/foundation.js"></script>

</body>
</html>