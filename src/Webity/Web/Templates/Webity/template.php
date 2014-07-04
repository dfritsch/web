<?php

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"
   xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
	<head>
		<?php echo $this->head; ?>
	</head>

	<body>
		<div class="header">
			<h3>Header Stuffs</h3>
		</div>
		<?php echo $this->content(); ?>
		<div class="footer">
			<h3>Footer Stuffs</h3>
		</div>
	</body>
</html>
