<?php
/* @var $this ChatController */
/* @var $model Chat */

$this->breadcrumbs=array(
	'Chats'=>array('index'),
	$model->name,
);
?>
<script>
function loadLog() {
	<?php echo CHtml::ajax(array('type'=>'POST', 'url'=>'ajaxUpdateTime', 'data'=>array('chat_id'=>$model->id), 'success'=>'js:function(html) {$("#useronline").html(html);}'));?>

	<?php echo CHtml::ajax(array('type'=>'POST', 'url'=>'ajaxGetMessages', 'data'=>array('id'=>$model->id), 'success'=>'js:function(html) {$("#chatbox").html(html);}'));?>
}

loadLog();
setInterval(loadLog, 1000);
</script>
<div class="row">
	<h1><?php echo $model->name; ?></hi>
</div>

<div class="chat">
<div class="useronline" id="useronline">
</div>

<div class="chatbox" id="chatbox">
</div>

<?php if (!Yii::app()->user->isGuest) : ?>
<div class="messagebox">
<?php echo CHtml::textField('message'); ?>
<?php echo CHtml::ajaxSubmitButton('Send', 'ajaxSendMessage', array('data'=>array('id'=>$model->id, 'message'=>'js:$("#message").val()'), 'success'=>'js:function(html){$("#message").val("");}'), array('id'=>'sendMessage')); ?>
</div>
<script>
$('#message').focus();

$(document).keypress(function (e) {
	if (e.which == 13) {
		$('#sendMessage').click();
	}
})
</script>
<?php endif ?>
</div>

<div class="character">
</div>
<?php  ?>
