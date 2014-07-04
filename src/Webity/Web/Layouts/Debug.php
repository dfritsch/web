<ul class="debug">
<?php
foreach ($displayData->debug as $action) {
	echo '<li>' . $action['message'] . ' at ' . $action['time'];
	if ($action['elapsed']) {
		echo ' - <strong>' . $action['elapsed'] . '</strong>';
	}
	echo '</li>';
}
?>
</ul>
