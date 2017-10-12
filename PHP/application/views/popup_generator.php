<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Required parameters(actiontitle, modalid, modalcontent)
?>


<div class="large reveal" id=<?php echo '"'.$modalid.'"' ?> data-reveal>
	<h5><?php echo $actiontitle ?></h5>
  <?php echo $modalcontent ?>
  <button class="close-button" data-close aria-label="Close modal" type="button">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<button class="button" data-open=<?php echo '"'.$modalid.'"' ?> > <?php echo $actiontitle ?> </button>
