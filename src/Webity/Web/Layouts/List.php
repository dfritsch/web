<table class="table">
    <tr>
        <?php foreach ($displayData['header'] as $key=>$val) : ?>
            <th><?= $val; ?></th>
        <?php endforeach; ?>
    </tr>
<?php if ($displayData['rows']) : ?>
    <?php foreach ($displayData['rows'] as $row) : ?>
        <tr>
            <?php foreach ($displayData['header'] as $key=>$val) : ?>
                <td><?= $row->$key; ?></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <tr>
        <td colspan="<?= count($displayData['header']); ?>">No records found.</td>
    </tr>
<?php endif; ?>
</table>
