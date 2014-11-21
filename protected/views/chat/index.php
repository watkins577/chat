<?php
/* @var $this ChatController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Chats',
);

if (!Yii::app()->user->isGuest)
	$this->menu=array(
		array('label'=>'Create Chat', 'url'=>array('create')),
	);
?>

<h1>Chats</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
