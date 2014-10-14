<?php

class ChatController extends Controller
{

	public $layout='//layouts/column1';
	
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Chat');
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
	}

	public function actionCreate()
	{
		$model = new Chat();

		if (isset($_POST['Chat'])) {
			$model->attributes = $_POST['Chat'];

			if ($model->save())
				$this->redirect(array('view', 'id'=>$model->id));
		}

		$this->render('create', array('model'=>$model));
	}

	public function actionView($id)
	{
		$model = $this->loadModel($id);
		$this->render('view', array('model'=>$model));
	}

	public function actionAjaxSendMessage()
	{
		$messageModel = new Message();

		$userModel = $this->loadUserByName(Yii::app()->user->id);

		$messageModel['chat_id'] = $_POST['id'];
		$messageModel['time_sent'] = time();


		if (strpos($_POST['message'], '/') === 0) {
			$messageModel['user_id'] = 1;

			$commandRet = $this->runCommand($_POST['message'], $userModel, $_POST['id']);
			$messageModel['message'] = isset($commandRet[0]) ? $commandRet[0] : null;
		} else {
			$messageModel['user_id'] = $userModel['id'];
			$messageModel['message'] = htmlspecialchars($_POST['message']);
		}
		if ($messageModel['message'] != null && $messageModel['message'] !== '') {
			if ($messageModel->validate())
				$messageModel->save();
			else
				var_dump($messageModel);
				var_dump($messageModel->getErrors());
		}
	}

	public function actionAjaxGetMessages()
	{
		$dataProvider = $this->loadMessagesByChat($_POST['id']);
		$this->renderPartial('_chat', array('dataProvider'=>$dataProvider));
	}

	public function actionAjaxUpdateTime()
	{
		$user_id = $this->loadUserByName(Yii::app()->user->id)['id'];
		$chat_id = $_POST['chat_id'];
		$model = UserOnline::model()->findByAttributes(array('user_id'=>$user_id, 'chat_id'=>$chat_id));

		if ($model == null) {
			$model = new UserOnline();
			$model['user_id'] = $user_id;
			$model['chat_id'] = $chat_id;
		}

		$model['last_time'] = time();

		if ($model->validate()) {
			$model->save();
		}

		$dataProvider = $this->loadCurrentUsers($chat_id);

		$this->renderPartial('_chatUsers', array('dataProvider'=>$dataProvider));
	}

	public function loadModel($id)
    {
        $model=Chat::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    public function loadUserByName($name)
    {
    	$model=User::model()->find('username=:username', array(':username'=>$name));
    	return $model;
    }

    public function loadMessagesByChat($id)
    {
    	$data = Message::model()->with('user')->findAllByAttributes(
    		array('chat_id'=>$id), 
    		array('order'=>'time_sent desc', 'limit'=>'10', 'offset'=>'0')
    	);
    	return $data;
    }

    public function loadCurrentUsers($id)
    {
    	$data = UserOnline::model()->with('user')->findAll(array(
    		'condition'=>'chat_id=:chat_id AND last_time>:cur_time', 
    		'params'=>array(':chat_id'=>$id, ':cur_time'=>(time()-10)))
    	);
    	return $data;
    }

    public function hasCharacter($user_id, $chat_id) {
    	$data = Character::model()->find(array(
    		'condition'=>'chat_id=:chat_id AND user_id=:user_id AND (status=1 OR status=2)',
    		'params'=>array(':chat_id'=>$chat_id, ':user_id'=>$user_id)
    		));

    	return $data != null;
    }

    public function runCommand($command, $user, $chat_id)
    {
    	$command = substr($command, 1);
    	if ($command == '') {
    		return null;
    	}
    	$commandParams = explode(' ', $command);
    	$ret = array();
    	if ($commandParams[0] == 'roll') {
    		$die = 6;
    		if (isset($commandParams[1])) {
    			$die = intval($commandParams[1]);
    		}
    		$ret[] = sprintf('%s has rolled a %s', $user['username'], rand(1, $die));
    	}
    	if ($commandParams[0] == 'create') {
    		if (isset($commandParams[1])) {
    			if (!$this->hasCharacter($user['id'], $chat_id)) {
    				$charModel = new Character();
    				$charModel['chat_id'] = $chat_id;
    				$charModel['user_id'] = $user['id'];

    				$charModel['name'] = htmlspecialchars($commandParams[1]);

    				if ($charModel->validate()) {
    					$charModel->save();
    				}
    				$ret[] = sprintf('%s has created a character named %s', $user['username'], $charModel['name']);
    			}
    		}
    	}
    	return $ret;
    }
}