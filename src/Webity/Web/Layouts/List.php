<table class="table table-striped">
    <tr>
        <?php foreach ($displayData['header'] as $key=>$val) : ?>
            <th><?= $val; ?></th>
        <?php endforeach; ?>
    </tr>
<?php foreach ($displayData['rows'] as $row) : ?>
    <tr>
        <?php foreach ($displayData['header'] as $key=>$val) : ?>
            <td><?= $row->$key; ?></td>
        <?php endforeach; ?>
    </tr>
<?php endforeach; ?>
</table>
