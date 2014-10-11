<?php
	$doc = $this->getDocument();
	$doc->addScript('//code.jquery.com/jquery-1.11.0.min.js');
?>
<div class="login-form">
	<form action="" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
		<div class="container-fluid">
				<fieldset class="">
					<h1 class="text-center"><span class="mimic-title">Mimic</span></h1>
					<div>
						<?php
							foreach($this->form->getFieldset('credentials') as $field) {
								echo $this->renderLayout('Field', $field);
							}
						?>

						<div class="control-group">
							<div class="controls">
								<input type="submit" value="Login" name="deck-submit" class="btn btn-primary" />
							</div>
						</div>
					</div>
				</fieldset>
		</div>
	</form>
</div>
