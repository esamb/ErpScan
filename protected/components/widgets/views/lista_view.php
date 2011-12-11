<?php
/**
 * Vista que despliega una lista de datos de cualquier tabla de la base de datos
 * la lista se mostrara en la parte izquierda de la pantalla.
 * Tiene la particularidad de hacer una buqueda dependiendo del parametro pasado  
 * en la declaracion del widget , esto le de la opcion al usuario de buscar 
 * para escoger el parametro
 * 
 * @author Jose Sambrano
 * company B.O.S
 */

?>
<div id="lista_datos">
    <input type="text" name="busqueda_datos" id="busqueda_datos" value=""><a href="#" onclick="showLoading()">Buscar</a>
    <br>
    <ul>
        <li>
            Testing
        </li>    
         <li>
            Testing1
        </li>    
         <li>
            Testing2
        </li>    
         <li>
            Testing3
        </li>    
         <li>
            Testing4
        </li>    
    </ul>
    <div id="mas_datos">
        <a href="#">Mostrar 10 siguientes</a>
    </div>
    <div id="loading"></div>
</div>   

<?php $this->widget('LoadingWidget'); ?>

<script>
    
function showLoading(){
    Loading.show();  
}
</script>