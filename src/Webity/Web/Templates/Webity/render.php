<?php 

if ($this->document) {
	$this->meta = $this->document->renderMeta();
	$this->head = $this->document->renderHeader();
} else {
	$this->meta = '';
	$this->head = '<title>Error generating header section</title>';
} ?>

<?php echo $this->content; ?>
