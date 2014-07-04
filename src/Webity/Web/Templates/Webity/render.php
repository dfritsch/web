<?php 

if ($this->document) {
	$this->head = $this->document->renderHeader();
} else {
	$this->head = '<title>Error generating header section</title>';
} ?>

<?php echo $this->content; ?>
