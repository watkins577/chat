<?php
/* @var $this ChatController */
/* @var $model Chat */

$this->breadcrumbs=array(
	'Chats'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Chat', 'url'=>array('index')),
);
?>

<h1>Create Chat</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>