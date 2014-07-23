<?php

$field = $displayData;

if (!$field) {
	return;
}

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
