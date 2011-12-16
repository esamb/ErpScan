<?php

/**
 * Objeto formulario que puede aceptar delegados en formato de arreglo u objetos de negocio o transferencia 
 * para resolver las propiedades requeridas
 *
 * @author vsayajin
 * @package components
 */
class DelegateForm extends CFormModel {

	public $_model = array();
	public $_rules = array();
	private $_readOnly = false;
	
	public function __construct($model = array(), $scenario = '') {
		$this->_model = $model;
		parent::__construct($scenario);
	}

	public function rules() {
		return $this->_rules;
	}

	public function addRule(array $rule) {
		$this->_rules[] = $rule;
	}

	/**
	 * PHP getter magic method.
	 * Trabaja primero con los atributos del modelo delegado, luego el objeto actual
	 * @param string property name
	 * @return mixed property value
	 * @see getAttribute
	 */
	public function __get($name) {
		if (is_object($this->_model) && property_exists($this->_model, $name))
			return $this->_model->$name;
		if (is_array($this->_model) && isset($this->_model[$name]))
			return $this->_model[$name];
		if (property_exists($this, $name))
			return $this->$name;
		return null;
	}

	/**
	 * PHP setter magic method.
	 * Trabaja primero con los atributos del modelo delegado, luego el objeto actual
	 * @param string property name
	 * @param mixed property value
	 */
	public function __set($name, $value) {
		if ($name == 'attributes')
			return $this->setAttributes($value);
		if($this->_readOnly)
			return;
		if ($this->checkModelProp($name))
			$this->_model->$name = $value;
		elseif (is_array($this->_model))
			$this->_model[$name] = $value;
		else
			$this->$name = $value;
	}

	public function setReadOnly($value){
		$this->_readOnly = $value;
	}
	
	public function getReadOnly(){
		return $this->_readOnly;
	}
	
	/**
	 * Comprueba si se puede asignar una propiedad al modelo delegado
	 * @param string $prop
	 * @return boolean
	 */
	protected function checkModelProp($prop) {
		if (!is_object($this->_model))
			return false;
		if ($this->_model instanceof stdClass)
			return true;
		return property_exists($this->_model, $prop);
	}

	public function isAttributeSafe($attribute){
		if($this->checkModelProp($attribute))
			return true;
		if(is_array($this->_model))
			return isset($this->_model[$attribute]);
		return property_exists($this, $attribute);  
	}

	public function attributeNames(){
		if(!$this->_model)
			return array();
		if(is_object($this->_model)){
			return array_keys(get_object_vars($this->_model));
		} elseif(is_array($this->_model)){
			return array_keys($this->_model);
		}
	}
	 
}

?>
