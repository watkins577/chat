<?php
/* @var $this ChatController */
/* @var $data Chat */
?>

<div class="view">

	<?php echo CHtml::link(CHtml::encode($data->name), array('view', 'id'=>$data->id)); ?>
	<br />


</div>