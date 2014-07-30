<?php
	$doc = $this->getDocument();
	$doc->addScript('//code.jquery.com/jquery-1.11.0.min.js');
?>

<form action="" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
	<div class="container-fluid">
		<div class="col-md-6">
			<fieldset class="adminform parentform">
				<legend>Login</legend>
				<div>
					<?php
						foreach($this->form->getFieldset('credentials') as $field) {
							echo $this->renderLayout('Field', $field);
						}
					?>

					<div class="control-group">
						<div class="controls">
							<input type="submit" value="save" name="deck-submit" class="btn btn-primary" />
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
