
<div class="text-center">
<?php
if ($displayData['start'] > 0) {
	echo '<a href="'.$displayData['base_url'].'?start='.($displayData['start'] - $displayData['limit']).'" class="btn btn-info"> <span class="glyphicon glyphicon-backward"></span> </a> ';
}

if ($displayData['more']) {
	echo '<a href="'.$displayData['base_url'].'?start='.($displayData['start'] + $displayData['limit']).'" class="btn btn-info"> <span class="glyphicon glyphicon-forward"></span> </a> ';
}
?>
</div>
