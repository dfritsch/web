<?php

	$form = $displayData;

	ob_start();
	foreach ($form->getFieldsets() as $fieldset) {
		echo '<fieldset name="'.$fieldset->name.'"';
		$class = array();
		$field = $value = '';
		if ($fieldset->multiple) {
			 $class[] = 'multiple';
		}
		if ($fieldset->dependency) {
			$class[] = 'dependency';
			$field = $fieldset->field;
			$value = $fieldset->value;
		}
		if ($class) {
			echo ' class="'. implode(' ', $class) . '"';
		}
		if ($field && $value) {
			echo ' data-field="'. $field . '" data-value="'. $value . '"';
		}
		if ($fieldset->copy) {
			echo ' data-copy="' . $fieldset->copy . '"';
		}
		echo '>';
		if ($fieldset->legend) {
			echo '<legend>'.$fieldset->legend.'</legend>';
		}
		if ($fieldset->soc) {
			echo '<p>This section should have a search or create option. Only one is currently shown.</p>';
		}
		//echo '<div class="edit-values">';
		foreach($form->getFieldset($fieldset->name) as $field):
			if (!$field->hidden && $field->display_value) {
			//	echo strip_tags($field->label) . ': <span class="' . str_replace(array('[',']'), array('_'),$field->name) . '">' . $field->value . '</span><br>';
			}
		endforeach;
		echo '<!--</div>-->
		<div class="edit-form">';
		foreach($form->getFieldset($fieldset->name) as $field):
			// If the field is hidden, only use the input.
	        if ($field->hidden):
	            echo $field->input;
	        elseif (strtolower($field->type) == 'editor'):
	        	?>
	        <div class="form-group">
	            <?php echo $field->label; ?>
				<?php echo $field->input; ?>
	        </div>
	        <?php
	        else:
	        ?>
	        <div class="form-group">
	            <?php echo str_replace('<label', '<label class="col-sm-2 control-label"', $field->label); ?>
				<div class="col-sm-10">
	            	<?php echo $field->input; ?>
				</div>
	        </div>
	        <?php
	        endif;
		endforeach;
		echo '<div class="clr"></div>';
		echo '</div>';
		echo '</fieldset>';
	}
	$html = ob_get_contents();
	ob_end_clean();

	echo $html;
