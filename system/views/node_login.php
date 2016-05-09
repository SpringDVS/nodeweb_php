<?php


?>
<div class="pure-u-1-5">&nbsp</div>
<div class="pure-u-3-5">
	
	<div class="white-container raised">
		<h3>Login</h3>
		<form class="pure-form pure-form-stacked" method="post">
			<label for="form-password"><strong>Node Password</strong></label>
			<input id="form-password" name="form-password" type="password">
			<button>Login</button>
		</form>
		<?php if($passState == 1): ?>
		<code style="color: #e9322d">Invalid Password</code>
		<?php endif; ?>
		
	</div>
	
	
</div>