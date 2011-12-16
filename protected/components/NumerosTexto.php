<?php

/**
 * Convierte números en letras principalmente para cuestiones legales o financieras
 * NOTA: Por ahora solo soporta hasta miles de millones, verificar, usa mi proceso de pensamiento
 * y no el común de los algoritmos 
 * @author vsayajin
 * @package components
 */
class NumerosTexto {
	/**
	 * Convierte un número (entero o decimal) en su representación en palabras en idioma español. 
	 * La implementación actual es válida para representación monetaria financiera. <br>
	 * El arreglo de opciones puede tener los valores:
	 * moneda : cadena o arreglo con el nombre en singular y plural de la moneda, ej array('Dólar', 'Dólares') 
	 * fraccion: cadena o arreglo con el nombre en singular y plural de la moneda fraccionadia, ej array('Centavo', 'Centavos') 
	 * force_cero: forza a procesar decimales aunque el valor sea cero, ej. ' con cero centavos'.
	 * @param string/number $cifra
	 * @param array $opciones
	 */
	function convertir($cifra, $opciones = array()){
		$t = (string)$cifra;
		$p = explode('.',$cifra, 2);
		$entero = $p[0];
		$fraccion = isset($p[1]) ? $p[1] : null;
		$force = !empty($opciones['force_cero']);
		$res['entero'] = $this->convertirEntero($entero, 'un');
		if($fraccion || $force)
			$res['fraccion'] = $this->convertirEntero($fraccion, 'un');
		$texto = $res['entero'];
		$texto .= ' ' . $this->resolverMonedaValor($res['entero'], $opciones, 'moneda', 'un');
		if($fraccion > 0 || $force){
			$texto .= ' con ' . $res['fraccion'];
			$texto .= ' ' .$this->resolverMonedaValor($res['fraccion'], $opciones, 'fraccionaria', 'un');
		}
		return trim($texto);
	}

	/**
	 * Construye una cadena utilizando opciones para los nombres de moneda y moneda fraccionaria con las
	 * opciones 'moneda' y 'fraccionaria' respectivamente.
	 * @param string $valor Texto salido de convertir
	 * @param array $opciones
	 * @param string $tipo tipo a buscar dentro de opciones
	 * @param srting $textoUno texto que representa al número uno
	 */
	function resolverMonedaValor($valor, $opciones, $tipo, $textoUno){
		if(!isset($opciones[$tipo]))
			return '';
		$posibles = $opciones[$tipo];
		$texto = '';
		if(is_array($posibles)) {
			if($valor == $textoUno) $texto = $posibles[0];
			else $texto = $posibles[1];
		} else
			$texto = $posibles;
		$txt = substr($valor , -3);
		if($txt == 'nes' || $txt == 'ón') 
			$texto = ' de '.$texto;
		return $texto;
	}

	/**
	 * Convierte un número entero en su representación en palabras del idioma español usando
	 * un algoritmo en reversa partiendo el número en grupos de 3 cifras.
	 * @param string/integer $cifra Número a convertir
	 * @param string $textoUno texto que representa al número uno
	 * @return string
	 */
	function convertirEntero($cifra, $textoUno = 'un'){
		$t = (string)$cifra;
		$un = explode(',',",$textoUno,dos,tres,cuatro,cinco,seis,siete,ocho,nueve"); // ojo con el manejo del uno
		$dec = explode(',',',:esp_dec,veinte,treinta,cuarenta,cincuenta,sesenta,setenta,ochenta,noventa');
		$cen = explode(',',',:esp_cen,dosc,tresc,cuatroc,quin,seisc,setec,ochoc,novec'); //ientos
		
		$calificador = array('','mil','mill','mil','bill','mil','trill','mil','cuatrill','mil','quintill', 'mil','sexti'); // ones u ón
		$esp_dec = explode(',',',once,doce,trece,catorce,quince,diesciseis,diecisiete,dieciocho,diecinueve');

		$l = array_reverse(str_split($t));
		$partes = array_chunk($l, 3);
		$todo = array();
		if($cifra < 0)
			$todo[] = 'menos';
		$i = 0;
		foreach($partes as $pos => $p){
			$words = array();
			$p0 = $p[0];
			$p1 = isset($p[1]) ? $p[1] : 0;
			$p2 = isset($p[2]) ? $p[2] : 0;
			$num = (int)($p2.$p1.$p0);
			$first = $un[$p0];
			$words[] = $first;
			$w = $dec[$p1];
			switch($w) {
				case '': break;
				case ':esp_dec':
					$words[] = $p[0] == '0' ? 'diez' : $esp_dec[$p0]; 
					$words[0] = '';
					break;
				default:
					/*if($w == 'veinte') // caso especial veinte
						$words[] = $p0 == '0' ? $w : 'veinti';
					else*/
						$words[] = $p0 == '0' ? $w : $w . ' y';
					break;
			}
			$w = $cen[$p2];
			switch($w) {
				case '': break;
				case ':esp_cen': // caso especial cien
					$words[] = ($p0 == '0' && $p1 == '0') ? 'cien' : 'ciento'; break;
				default:
					$words[] = $cen[$p2] . 'ientos';
					break;
			}
			// manejo postfijo y uno con calificadores en millones o más
			$post = $calificador[$i];
			$i++;
			if($num == 1) {
				if($post == 'mil')
					$words[0] = 'mil';
				elseif($post)
					$words[0] = $post . 'ón';
			} else {
				if($post != '' && $post != 'mil')
					$post .= 'ones';
				if($num > 0)
					array_unshift($words, $post);
			}
			if($words)
				$todo = array_merge($todo, $words);
		}
		$texto = trim(implode(' ',array_reverse($todo)));
		$texto = str_replace('veinte y ', 'veinti', $texto);
		if(!$texto) $texto = 'cero';
		return $texto;
	}
}

?>
