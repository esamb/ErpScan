<?php

/**
 * Corresponde a un evento del sistema registrado en la tabla auditoria
 *
 * @property integer $id
 * @property string $nivel
 * @property string $categoria
 * @property timestamp $logtime
 * @property string $mesnsaje
 * @property string $data
 * @property string $usuario
 * @property string $ip
 * 
 * @package models
 */
class Auditoria extends CActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return "AUDITORIA";
	}
	
	public function rules() {
		return array(
			array('nivel, categoria, logtime, mensaje, data, usuario, ip', 'safe'),
		);
	}
	
	public function search($pageSize=0) {
		$criteria = new CDbCriteria;
		$criteria->compare('nivel', $this->nivel);
		$criteria->compare('categoria', $this->categoria);
		$criteria->compare('mensaje', $this->mensaje, true);
		$criteria->compare('data', $this->data, true);
		$criteria->compare('usuario', $this->usuario);
		$criteria->compare('ip', $this->ip, true);
		$criteria->order = 'logtime desc';

		$options = array('criteria' => $criteria);
		if ($pageSize > 0)
			$options['pagination']['pageSize'] = $pageSize;

		return new CActiveDataProvider(get_class($this), $options);
	}

	public static function error($mensaje, $category, $data='', $includeSystem = false) {
		$a = Auditoria::getNewLog($mensaje, CLogger::LEVEL_ERROR, $category);
		if ($data instanceof Exception) {
			$a->data = $data->getTraceAsString();
		} else if ($data)
			$a->data = $data;
		$a->save();
	}

	public static function info($mensaje, $category, $data='', $includeSystem = false) {
		$a = Auditoria::getNewLog($mensaje, CLogger::LEVEL_INFO, $category);
		if ($data)
			$a->data = $data;
		$a->save();
	}

	public static function warning($mensaje, $category, $data='', $includeSystem = false) {
		$a = Auditoria::getNewLog($mensaje, CLogger::LEVEL_WARNING, $category);
		if ($data)
			$a->data = $data;
		$a->save();
	}

	public static function getNewLog($mensaje, $level, $category, $includeSystem = false) {
		$a = new Auditoria('insert');
		$a->nivel = $level;
		$a->mensaje = $mensaje;
		$a->categoria = $category;
		$a->logtime = DateUtils::now(); 
		$a->usuario = Yii::app()->user->id;
		$a->ip = Yii::app()->getRequest()->getUserHostAddress();

		if ($includeSystem)
			Yii::log($mensaje, $level, $category);
		return $a;
	}

}