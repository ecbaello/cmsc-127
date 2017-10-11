<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Required parameters(link, fields)
?><form action=<?php echo '"'.$link.'"' ?> method="post">
	<?php if ( isset($tablename) ) echo '<input type="hidden" name="table" value='.$tablename.'>' ?>
	<?php if ( isset($extrahtml) ) echo $extrahtml ?>
	<?php
		foreach ($fields as $key => $value) {
			echo '<label for="'.$key .'">'.$value.'</label><input type="text" name="'.$key.'" >';
		}

	?>
	<input class="button" type="submit" value="Insert">
</form>

