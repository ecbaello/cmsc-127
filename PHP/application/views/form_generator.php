<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Required parameters(link, fields)

$newline		= "\n";

?><form action=<?php echo '"'.$link.'"' ?> method="post">
	<div class="grid-x grid-padding-x">
		<?php if ( isset($tablename) ) echo '<input type="hidden" name="table" value='.$tablename.'>'.$newline ?>
		<?php if ( isset($extrahtml) ) echo $extrahtml.$newline ?>
		<?php
			if (!empty($constants) && is_array($constants))
				foreach ($constants as $key => $value) {
					echo '<input type="hidden" name="'.$key.'" value="'.$value.'">'.$newline;
				}
			foreach ($fields as $key => $value) {
				echo '<div class="small-12 medium-6 large-6 cell">'.$newline;
				echo '<label for="'.$key .'">'.$value.'<input type="text" name="'.$key.'" ></label>'.$newline;
				echo '</div>'.$newline;
			}
		?>
	</div>
	<div class="grid-padding-x" style="text-align: center;"><input class="button" style="width: 100%" type="submit" value="Insert"></div>
</form>

