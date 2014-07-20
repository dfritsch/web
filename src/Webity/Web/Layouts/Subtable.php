<?php

$link_name = $displayData['link'];
$id = $displayData['id'];
$model = $displayData['model'];
$link_items = $model->getSubItems($id);

$return = '<div class="tabs '.$link_name.'">
	<a data-fieldset="'.$link_name.'" data-update="append" data-controller="'.$link_name.'" id="'.$link_name.'add" class="inline-edit-trigger btn btn-success add-btn"><i class="icon-plus-sign"></i> Add</a>
	<ul>
		<li class="published"><a href="#'.$link_name.'-published">Published <i class="icon-ok"></i></a></li>
		<li class="trashed"><a href="#'.$link_name.'-trashed">Trashed <i class="icon-trash"></i></a></li>
	</ul>
	<div id="'.$link_name.'-published" class="sortable">';

if (is_array($link_items)) {
	foreach ($link_items as $key=>$link) {
        $string = '';
        if (!$link_name) {
			return '';
		}
		$values = array();
		$string .= '<fieldset id="'.$link_name.$key.'"><ul class="values">';
		foreach ($link->getFieldset($link_name) as $field) {
			if ($field->__get('type') != 'Hidden') {
				// for the label, remove the 'required' label and then strip the label tag.
				// our field types have a displayValue function, especially for sql fields.
				$string .= '
				<li><span class="value-label">'.strip_tags(str_replace('<span class="star">&#160;*</span>', '', $field->__get('label'))).':</span> <span class="value">'. ( method_exists($field, 'getDisplayValue') ? $field->getDisplayValue() : $field->__get('value') ) .'</span></li>';
	        }
         	$values[$field->__get('name')] = $field->__get('value');
         	if (strpos($field->__get('name'), '[id]') !== FALSE) {
         		$id = $field->__get('value');
         	}

			if (strpos(strtolower($field->__get('name')), '[state]') !== FALSE) {
				$state = $field->__get('value');
			}

         	if ($check_trashed && !$trash_check[$link_name] && $state == -2) {
         		if ($field->__get('value') != 1) {
         			$string = '</div><div id="'.$link_name.'-trashed">' . $string;
         			$trash_check[$link_name] = 1;
         		}
         	}
		}
		$string .= '</ul><input type="hidden" name="record_values" class="record_values" value=\''.json_encode($values).'\' /><input type="hidden" name="ajax_id" class="id" value="'.$id.'" /><div class="clr"></div>';


		$string .= '<a data-fieldset="'.$link_name.'" data-update="'.$link_name.$key.'" data-controller="'.$link_name.'" id="'.$link_name.'add"  class="inline-edit-trigger edit-item btn btn-primary"><i class="icon-pencil"></i> Edit</a> ';
		if ($state == 1 || !isset($state)) {
			$string .= '<a id="'.$link_name.'delete" data-fieldset="'.$link_name.'" class="state-change delete btn btn-danger"><i class="icon-remove"></i> Remove</a>';
		} else {
			$string .= '<a data-fieldset="'.$link_name.'" id="'.$link_name.'restore"  class="state-change restore btn btn-success"><i class="icon-refresh"></i> Restore</a>';
		}

		$string .= '</fieldset>';

		$return .= $string; //$this->link_html($link_name, $key, $link, true);
	}
}

if (strpos($return, '<div id="'.$link_name.'-trashed">') === FALSE) {
	$return .= '</div><div id="'.$link_name.'-trashed">';
}

$return .= '</div></div>';

echo $return;
