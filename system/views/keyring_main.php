<?php 
	$handler = new KeyringHandler();

	$keys = $handler->getNodePublicKey();
?>

<div class="pure-u-3-5" style="margin-left: 15px;">
	<textarea rows="25" cols="64" class="key-display"><?php 
		echo $keys['armor'];
	?></textarea>
</div>


<?php
