<?php

use Webity\Web\Application\WebApp;
use Webity\Web\Layout\File as Layout;

$app = WebApp::getInstance();
// $base is needed because we use a base tag
$base = $app->get('uri.route');

$link_name = $displayData['link'];
$id = $displayData['id'];
$model = $displayData['model'];
$link_items = $model->getSubItems($id);

$return = '<div class="tabs '.$link_name.'">
	<a data-fieldset="'.$link_name.'" data-update="append" data-controller="'.$link_name.'" id="'.$link_name.'add" class="inline-edit-trigger btn btn-success add-btn"><i class="icon-plus-sign"></i> Add</a>
	<ul>
		<li class="published"><a href="'.$base.'#'.$link_name.'-published">Published <i class="icon-ok"></i></a></li>
		<li class="trashed"><a href="'.$base.'#'.$link_name.'-trashed">Trashed <i class="icon-trash"></i></a></li>
	</ul>
	<div id="'.$link_name.'-published" class="sortable">';

if (is_array($link_items)) {
	foreach ($link_items as $key=>$link) {
		// hack for the trash check...
		$check_trash = strpos($return, '<div id="'.$link_name.'-trashed">') ? false : true;

        $layout = new Layout('SubtableHtml');
		$return .= $layout->render(array(
			'link_name' => $link_name,
			'key' => $key,
			'link' => $link,
			'check_trashed' => $check_trash
		));
	}
}

if (strpos($return, '<div id="'.$link_name.'-trashed">') === FALSE) {
	$return .= '</div><div id="'.$link_name.'-trashed">';
}

$return .= '</div></div>';

echo $return;
