<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ManagerLista extends CApplicationComponent{
    
        /*
         * Numero total de registros que se muestran al usuario 
         * 
         * nombre de la variable fue seteado al nombre de yii.
         * @$limit static int
         *  
         */
        public static $limit=10;
        
        public $pagina_actual=1;
        
        /*
         * Numero total de registros que se muestran al usuario
         * nombre de la variable fue seteado al nombre de yii.
         * @$limit static int
         *  
         */
        
        public $offset;
        
    public function lista(){
        $conds = array();
        $c = new CDbCriteria();
    }
    public function cargaJson(){
        
        
    }
    
}
?>
