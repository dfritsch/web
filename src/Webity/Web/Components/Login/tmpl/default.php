<?php
	$doc = $this->getDocument();
	$doc->addScript('//code.jquery.com/jquery-1.11.0.min.js');
?>

<form action="" method="post" role="form" class="form-horizontal login" enctype="multipart/form-data">
	<div class="container-fluid">
		<div class="col-md-6">
			<fieldset class="adminform parentform">
				<legend>Login</legend>
                <hr />
				<div>
					<?php
						foreach($this->form->getFieldset('credentials') as $field) {
							echo $this->renderLayout('Field', $field);
						}
					?>

					<div class="control-group submit-group">
						<div class="controls">
							<button type="submit" value="save" name="submit" class="btn btn-primary">Login</button>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
