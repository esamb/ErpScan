<?php
/**
 * Objeto de transferencia (DTO) para guardar de forma estructurada la información del usuario
 * actual del sistema, EXPERIMENTAL
 *
 * @author vsayajin
 * @package components
 */
class InfoUsuario {
    var $id;
	var $username;
	var $nombreCompleto;
	var $rol;
	var $casa_id;
	var $local_id;

	function __construct(Usuario $usuario= null) {
		if($usuario){
			$this->id = $usuario->id;
			$this->username = $usuario->username;
			$this->nombreCompleto = $usuario->nombreCompleto();
			if($usuario->rol)
				$this->rol = $usuario->rol->codigo;
			$this->casa_id = $usuario->casa_id;
			$this->local_id = $usuario->ultimolocal_id;
		}
	}

}
?>
