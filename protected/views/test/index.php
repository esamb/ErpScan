<?php $this->pageTitle=Yii::app()->name; ?>

TOTAL: <?php echo count($rows) ?>;

<table>
<?php foreach($rows as $row):?>
<tr>
	<th><?php echo $row['id']?></th>
	<th><?php echo $row['nombre']?></th>
	<th><?php echo $row['username']?></th>
</tr>
<?php endforeach; ?>
</table>
