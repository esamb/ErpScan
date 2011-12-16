<?php

/**
 * Vista de los eventos del sistema registrador en la tabla auditoria
 * @package controllers
 * @author vsayajin
 *
 */
class AuditoriaController extends AdminController {

	public function init() {
		parent::init();
	}

	public function actionIndex() {
		$cat = new Auditoria('search');
		if (isset($_POST['Auditoria']))
			$cat->attributes = $_POST['Auditoria'];
		$dataProvider = $cat->search(20);
		$this->render('index', array('provider' => $dataProvider));
	}

	public function actionView() {
		$this->render('view', array('model' => $this->loadModel()));
	}

	protected function loadModel() {
		$id = Yii::app()->request->getParam('id');
		if (!$id)
			throw new CHttpException(500, 'Entidad no encontrada');
		return Auditoria::model()->findByPk($id);
	}

}