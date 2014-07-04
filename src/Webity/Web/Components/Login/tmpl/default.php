<?php
	$doc = $this->getDocument();
	$doc->addScript('//code.jquery.com/jquery-1.11.0.min.js');
?>

<form action="" method="post">
	<?php echo $this->renderLayout('Form', $this->form); ?>

	<div class="control-group">
		<div class="controls">
			<input type="submit" value="Login" name="login-submit" />
		</div>
	</div>
</form>
