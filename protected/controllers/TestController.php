<?php

class TestController extends Controller
{
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$db = Yii::app()->db;
		$cmd = $db->createCommand('select * from usuario');
		$rows = $cmd->queryAll();
		$this->render('index', compact('rows'));
	}

	

}