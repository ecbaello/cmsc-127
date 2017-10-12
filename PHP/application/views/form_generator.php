<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Required parameters(link, fields)

$newline		= "\n";

?><form action=<?php echo '"'.$link.'"' ?> method="post">
	<?php if ( isset($tablename) ) echo '<input type="hidden" name="table" value='.$tablename.'>'.$newline ?>
	<?php if ( isset($extrahtml) ) echo $extrahtml.$newline ?>
	<?php
		foreach ($fields as $key => $value) {
			echo '<label for="'.$key .'">'.$value.'</label><input type="text" name="'.$key.'" >'.$newline;
		}

	?>
	<input class="button" type="submit" value="Insert">
</form>

