<?php
/**
 * 
 *
 * @author josesambrano
 * @company B.O.S
 * @package components.widgets
 */
class ListaWidget extends CWidget {
	/*
         * El modelo de la vista la cual queremos mostrar
         * @$model CModel
         * 
         */        
        public $model;
        /*
         * Array de los datos que se van a mostrar al usuario
         * @$model array
         */
        public $show_field=array();
        
         /*
         * Nombre del id de la tabla , sirve para hacer la busqueda
         * de los datos del modelo
         * 
         * @$id string
         */
        public $id;
        /*
         * Array asociativo , permite parametrizar si los parametros 
         * del show_field se necesitan parametrizar y si necesitan ser 
         * contatenados con algun caracter en especial
         * @$extra_fields array
         */
        public $extra_fields=array(
            'concatena'=>false,
            'caracter_concatenar'=>' ',
        );
        
        public $parametros_busqueda=array();
        
        public static $datos_per_page=10;
        
        public $set_actual=1;
	
	public function init() {
		parent::init();
	}

	public function run() {
                parent::run();
            
        	$this->render('lista_view', $model);
	}

}

