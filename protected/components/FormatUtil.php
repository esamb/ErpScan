<?php

/**
 * Clase utilitaria que tiene funciones de formateo y auxiliares
 *
 * @author vsayajin
 * @package components
 */
class FormatUtil extends CComponent {

	/**
	 * Formatea un valor de fecha/tiempo utilizando una cadena de acuerdo a los formatos soportados por
	 * la clase CDateFormatter de Yii
	 * @param mixed $value Puede ser una cadena, un numero o DateTime
	 * @param string $format
	 * @return string
	 */
	public static function formatDateYii($value, $format = 'yyyy-MM-dd') {
		if (!$value)
			return '';
		if ($value instanceof DateTime)
			$value = $value->format('U'); // timestamp
		$f = Yii::app()->getDateFormatter();
		return $f->format($format, $value);
	}

	/**
	 * Formatea un valor de fecha/tiempo utilizando una cadena de acuerdo a los formatos soportados por
	 * la clase DateTime de plataforma
	 * @param mixed $value Puede ser una cadena, un numero o DateTime
	 * @param string $format
	 * @return string
	 */
	public static function formatDate($value, $format='Y-m-d') {
		if (!$value)
			return '';
		if ($value instanceof DateTime)
			return $value->format($format);
		$d = new DateTime($value);
		return $d->format($format);
	}

	public static function formatDateLocale($value, $format='%F') {
		if (!$value)
			return '';
		if (!($value instanceof DateTime))
			$value = new DateTime($value);
		$ts = $value->format('U');
		return strftime($format, $ts);
	}

	/**
	 * Trunca una cadena si es mayor que una longitud máxima, opcionalmente pone un postfijo al texto
	 * @param string $string
	 * @param integer $max
	 * @param string $postfixque poner al final de la cadena truncada
	 * @return string 
	 */
	public static function truncate($string, $max, $postfix='...') {
		if (!$string || !is_string($string))
			return '';
		return strlen($string) > $max ? substr($string, 0, $max) . $postfix : $string;
	}

	/**
	 * Formatea un número como porcentaje incluyendo el símbolo %, redondea 2 dígitos
	 * @param float $valor
	 * @param boolean $fraction Si es verdadero, multiplica el valor por 100, sino lo deja intacto
	 * @return string
	 */
	public static function porcentaje($valor, $fraction=true) {
		if ($valor == '' || $valor == null)
			return $valor;
		if ($fraction)
			$valor *= 100;
		return self::number($valor) . '%';
	}

	/**
	 * Formatea un número truncando su valor decimal a elección usando
	 * el caracter . para separar decimales.
	 * Utiliza la función nativa de PHP {@see number_format}.  
	 * @param number $valor Número a formatear
	 * @param integer $decimals Número de decimales a truncar
	 */
	public static function number($valor, $decimals = 2) {
		if ($valor == '' || $valor == null)
			return $valor;
		return number_format($valor, $decimals, '.', '');
	}

	/**
	 * Convierte un array en un objeto de forma recursiva si se define el parametro depth mayor a 0.
	 * Si se quiere que el objeto tenga una clase específica, se debe asignar una propiedad llamada __class
	 * a una cadena (nombre de clase) o un objeto ya creado
	 * @param array $array
	 * @param integer $depth Nivel de profundidad para procesar
	 * @return stdClass objeto genérico
	 */
	public static function arrayToObject(array $array, $depth = 0, $outClass = 'stdClass') {
		$class = isset($array['__class']) ? $array['__class'] : $outClass;
		if (!$class)
			$obj = new stdClass();
		elseif (is_object($class))
			$obj = $class;
		elseif (is_string($class)) {
			if ($class == 'array')
				return $array;
			$obj = new $class;
		}
		foreach ($array as $key => $value) {
			if (is_array($value) && $depth > 0)
				$obj->$key = self::arrayToObject($value, $depth - 1);
			else
				$obj->$key = $value;
		}
		return $obj;
	}

	/**
	 * Llena un objeto usando los valores de un array asociativo
	 * @param object $obj objeto
	 * @param array $array valores
	 * @param array $include_only Lista de atributos a incluir, opcional
	 * @return object 
	 */
	public static function fillObject($obj, $array, $include_only = array(), $force = false) {
		if (!$obj || !$array || !is_array($array))
			return $obj;
		foreach ($array as $key => $value) {
			if ($include_only && !in_array($key, $include_only))
				continue;
			$set = $force ? true : property_exists($obj, $key) || ($obj instanceof stdClass);
			if ($set)
				$obj->$key = $value;
		}
		return $obj;
	}

	/**
	 * Devuelve un array con los atributos de un objeto. 
	 * NOTA: Puede ser innecesario ya que un simple casting a (array) hace lo mismo, revisar
	 * @param object $obj
	 * @param array $attributes
	 */
	public static function extractFromObject($obj, $attributes = array()) {
		$res = array();
		if (!$attributes)
			$attributes = array_keys(get_object_vars($obj));
		foreach ($attributes as $att) {
			if (property_exists($obj, $att))
				$res[$att] = $obj->$att;
		}
		return $res;
	}

	/**
	 * Deserializa cadenas de texto de forma más segura comprobando si es texto o un
	 * resource, esto último por culpa de los blobs de ciertas bases de datos. 
	 * Los formatos aceptados son 'php' y 'json'
	 * @param mixed $value string o resource para deserializar
	 * @param mixed $default valor por defecto si no hay datos
	 * @param string $format por defecto 'php'
	 * @return mixed
	 */
	public static function safeUnserialize($value, $default = null, $format = 'php') {
		if (empty($value))
			return $default;
		$text = is_resource($value) ?
				stream_get_contents($value) : $value;
		if ($text == null || $text == '')
			return $default;
		switch ($format) {
			case 'php': return unserialize($text);
			case 'json' : return CJSON::decode($text);
			default : throw new Exception("Formato $format de deserialización desconocido");
		}
	}

	/**
	 * Devuelve un arreglo con opciones por defecto para ser utilizadas por el componente {@see SelectorDatepickerWidget}
	 * para habilitar un calendario dinámico en campos de texto. Los valores por defecto configuran al calendario
	 * para que esté en español, la fecha esté en formato ISO, muestre combos de meses y años y genere una función reutilizable. 
	 * El argumento $tipo puede tener tres opciones:
	 * 'fecha': todas las fechas, selector jQuery 'input.fecha'
	 * 'pasado': solo fechas antes de hoy, selector 'input.fecha-pasado'
	 * 'futuro': solo fechas luego de hoy, selector 'input.fecha-futuro'
	 * @param string $tipo 
	 * @param array $adicionales Opciones extra que se combinarán con las opciones por defecto
	 * @return array Opciones para el widget de fechas
	 */
	public static function defaultDateOptions($tipo = 'fecha', $adicionales = array()) {
		$dateoptions = array(
			'selector' => 'input.fecha',
			'function' => 'date_set',
			'language' => 'es',
			'options' => array(
				'dateFormat' => 'yy-mm-dd',
				'changeMonth' => true,
				'changeYear' => true,
				'buttonImage' => Yii::app()->baseurl . '/images/icons/calendar.png',
				'buttonImageOnly' => true,
				'showOn' => 'both',
				'buttonText' => 'Ver Calendario'
			)
		);
		if ($tipo == 'pasado') {
			$dateoptions['function'] = 'date_set_pasado';
			$dateoptions['selector'] = 'input.fecha-pasado';
			$dateoptions['options']['maxDate'] = DateUtils::now();
		}
		if ($tipo == 'futuro') {
			$dateoptions['function'] = 'date_set_futuro';
			$dateoptions['selector'] = 'input.fecha-futuro';
			$dateoptions['options']['minDate'] = DateUtils::now();
		}
                if($tipo=='tabla'){
                    	$dateoptions['function'] = 'date_set_futuro';
			$dateoptions['selector'] = 'input.fecha-futuro';
                        
			$dateoptions['options']['minDate'] = DateUtils::now();
                        $dateoptions['options']['maxDate'] = DateUtils::future();


                }
		if ($adicionales)
			$dateoptions = array_merge($dateoptions, $adicionales);
		return $dateoptions;
	}

	/**
	 * Busca los valores corrspondientes a claves dentro de un array de n niveles utilizando una expresion 
	 * tipo punto (propiedad.propiedad.) como si se tratara de propiedades en un lenguaje menos fiero que este
	 * @param array $ctx Array para buscar
	 * @param string $prop Expresion a buscar, ej. 'sesion.nombre'
	 * @param mixed $default valor por defecto si no se encuentra el valor
	 * @return mixed
	 */
	public static function getProp($ctx, $prop, $default = null) {
		$prop = trim($prop);
		if($prop == null || $prop == '')
			return $default;
		$partes = explode('.', $prop);
		$actual = $ctx;
		while (count($partes) > 0) {
			$parte = array_shift($partes);
			if (!isset($actual[$parte]))
				return $default;
			$actual = $actual[$parte];
			if (is_array($actual))
				continue;
			if (is_object($actual)) {
				$actual = (array) $actual;
				continue;
			}
			return $actual;
		}
		return $default;
	}
	
	/**
	 * Normaliza una cadena removiendo los acentos
	 * @param string $string
	 * @return string
	 */
	public static function normalizeString($string) {
		return StringNormalizer::normalize($string);
	}



        /*
         * formatea la tabla para el pagare.
         */
        public static function trataTablaAmortizacion($tabla){
            $res=array();
            $temp=0;
            
            foreach($tabla->pagos as  $valores){
                $temp+=$valores->dias;
                $res[$valores->numero][0]=$temp;
                $res[$valores->numero][1]=number_format((float)$valores->cuota,"2");
            }
            return $res;
        }

}

/**
 * Clase auxiliar para normalizar (remover acentos) una cadena;
 * @package components
 */
class StringNormalizer {

	static $charset = array(
		'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
		'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
		'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
		'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
		'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
		'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
		'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f'
	);

	public static function normalize($string) {
		return strtr($string, self::$charset);
	}

}

?>
