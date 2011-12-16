<?php
/**
 * Representa la incormación sobre la validación de cedulas y rucs emitidas
 * por {@link ValidacionCedulaRuc::procesar}
 * @package components
 */
class ValidacionCedulaResponse{
	public $ruc = false;
	public $numero;
	public $mensaje;
	public $valido = false;
	public $tipo;
}

/**
 * Componente que valida las cédulas y RUCs emitidos por el estado ecuatoriano.
 * Utiliza un peculiar algoritmo basado el módulos y coeficientes para generar un dígito verificador que forma parte del número mismo.
 * Funciona para cédulas de identidad, rucs de personas naturales, empresas privadas y publicas.
 *
 * @author vsayajin
 * @package components
 */
class ValidacionCedulaRuc{
	/**
	 * Mantiene los coeficientes para cada tipo de entidad
	 * @var array */
	public static $config = array();
	public static $instance = null;
	const NUMERO_PROVINCIAS = 22;
	/**
	 * Inicialización estática de las configuraciones a petición
	 */
	protected function init() {
		if (!self::$config) {
			self::$config = array(
				'natural' => array(2, 1, 2, 1, 2, 1, 2, 1, 2),
				'privada' => array(4, 3, 2, 7, 6, 5, 4, 3, 2),
				'publica' => array(3, 2, 7, 6, 5, 4, 3, 2)
			);
		}
	}

	/**
	 * Genera un objeto de respuesta con infomación de la validación
	 * @return ValidacionCedulaResponse
	 */
	protected function response($numero, $mensaje, $ruc=false, $valido=false, $tipo='') {
		$res = new ValidacionCedulaResponse;
		$res->numero = $numero;
		$res->mensaje = $mensaje;
		$res->ruc = $ruc;
		$res->valido = $valido;
		$res->tipo = $tipo;
		return $res;
	}

	/**
	 * Ejecuta la validación de una cadena con un posible numero de cedula o ruc y retorna
	 * un objeto con información sobre el documento
	 * @param string $cedula
	 * @return ValidacionCedulaResponse objeto de respuesta con información de la validación
	 */
	public function procesar($cedula){
		if(!$cedula)
			return $this->response($cedula, 'Cédula vacía');
		$len = strlen($cedula);
		if (!is_numeric($cedula))
			return $this->response($cedula, 'Todos los caracteres deben ser números');
		if (!($len == 10 || $len == 13))
			return $this->response($cedula, 'Longitud inadecuada');
		$esruc = $len == 13;
		$suma = 0;
		$residuo = 0;
		$modulo = 11;

		$d = array();
		for ($i = 0; $i < 10; $i++)
			$d[$i] = (int)$cedula[$i];

		if ($d[2]==7 || $d[2]==8)
			return $this->response($cedula, 'El tercer dígito ingresado es inválido', $esruc);

		$prov = substr($cedula, 0, 2);
		if($prov < 1 || $prov > self::NUMERO_PROVINCIAS)
			return $this->response('El código de la provincia (dos primeros dígitos) es inválido', $esruc);

		//$p = array();
		$p = array_fill(0, 9, 0); // llenar de ceros
		if ($d[2] < 6) {
			$tipo = 'natural';
			$modulo = 10;
		} else if($d[2] == 6) $tipo = 'publica';
		else if($d[2] == 9) $tipo = 'privada';

		$this->init();
		$coeficientes = self::$config[$tipo];

		foreach($coeficientes as $i => $coef){
			$p[$i] = $d[$i] * $coef;
			if($tipo == 'natural' && $p[$i] >= 10)
				$p[$i] -= 9;
		}
		//if($tipo == 'publica') $p[8] = 0;

		$suma = array_sum($p);
		$residuo = $suma % $modulo;
		/* Si residuo=0, dig.ver.=0, caso contrario 10 - residuo*/
		$digitoVerificador = $residuo==0 ? 0: $modulo - $residuo;

		// verificacion de ultimos digitos del ruc? puede haber varios locales
		if($esruc && substr($cedula, 10, 3) == '000')
			return $this->response($cedula, 'El ruc no puede terminar con ceros.', $esruc, false, $tipo);

		switch($tipo){
			case 'natural':
				if ($digitoVerificador != $d[9] || $cedula == '2222222222') // caso especial
					return $this->response($cedula, 'El número de cédula de la persona natural es incorrecto.', $esruc, false, $tipo);
				break;
			case 'privada':
				if ($digitoVerificador != $d[9])
					return $this->response($cedula, 'El ruc de la empresa del sector privado es incorrecto.', $esruc, false, $tipo);
				break;
			case 'publica':
				if ($digitoVerificador != $d[8])
					return $this->response($cedula, 'El ruc de la empresa del sector público es incorrecto.', $esruc, false, $tipo);
				break;
		}

		return $this->response($cedula, 'OK', $esruc, true, $tipo);
	}

	/**
	 * Función conveniente para obtener información de una cédula o ruc del estado ecuatoriano
	 * @param string $cedula
	 * @return ValidacionCedulaResponse Objeto con información sobre la validación
	 */
	public static function procesarDocumento($cedula) {
		if(!self::$instance)
			self::$instance = new ValidacionCedulaRuc;
		return self::$instance->procesar($cedula);
	}

	/**
	 * Función simple para validación de cédulas y rucs, devuelve verdadero o falso
	 * @param string $cedula
	 * @return boolean si es válido o no
	 */
	public static function esDocumentoValido($cedula) {
		$res = self::procesarDocumento($cedula);
		return $res->valido;
	}

}

?>