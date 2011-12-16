<?php

/**
 * Clase utilitaria para el manejo de fechas utilizando principalmente la clase de plataforma DateTime
 * @author vsayajin
 * @package components
 */
class DateUtils {

	/**
	 * Clona un objeto de tipo datetime
	 * @param DateTime $date
	 * @return DateTime nuevo objeto
	 */
	public static function cloneDate(DateTime $date) {
		return new DateTime($date->format('c'));
	}

	/**
	 * Devuelve la diferencia entre dos fechas como un array con los componentes del intervalo
	 * fuente: http://forums.webmasterhub.net/viewtopic.php?f=23&t=1831. 
	 * Debería ser reemplazado por DateInterval si el PHP es >= 5.3
	 * El array contiene los siguientes índices cuyos valores son numéricos:
	 * years, months_total, months, days_total, days, hours_total, hours,
	 * minutes_total, minutes, seconds_total, seconds.
	 *  
	 * @param DateTime $newest 
	 * @param DateTime $oldest
	 * @return array
	 */
	public static function dateDiff(DateTime $newest, DateTime $oldest) {
		$d1 = $newest->format('U');
		$d2 = $oldest->format('U');
		$diff_secs = abs($d1 - $d2);
		$base_year = min(date("Y", $d1), date("Y", $d2));

		$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);

		return array
			(
			"years" => abs(substr(date('Ymd', $d1) - date('Ymd', $d2), 0, -4)),
			"months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,
			"months" => date("n", $diff) - 1,
			"days_total" => floor($diff_secs / (3600 * 24)),
			"days" => date("j", $diff) - 1,
			"hours_total" => floor($diff_secs / 3600),
			"hours" => date("G", $diff),
			"minutes_total" => floor($diff_secs / 60),
			"minutes" => (int) date("i", $diff),
			"seconds_total" => $diff_secs,
			"seconds" => (int) date("s", $diff)
		);
	}

	/**
	 * Devuelve una cadena con la fecha actual en un formato reconocido por date()
	 * @param string $format Formato opcional, por defecto es ISO ('Y-m-d H:i:s)
	 * @return string Fecha en el formato determinado
	 */
	public static function now($format = 'Y-m-d H:i:s') {
		$d = new DateTime();
		return $d->format($format);
	}

        /**
	 * Devuelve una cadena con la fecha pasado 45 dias en un formato reconocido por date()
	 * @param string $format Formato opcional, por defecto es ISO ('Y-m-d H:i:s)
	 * @return string Fecha en el formato determinado
	 */
	public static function future($format = 'Y-m-d H:i:s') {
		$d = new DateTime();
                $d->modify('+45 days');
		return $d->format($format);
	}
	/**
	 * Comprueba que una cadena sea una fecha en formato ISO que puede incluir tiempo
	 * @param string $value
	 * @return boolean Si el valor está en formato ISO o no
	 */
	public static function isIsoDate($value){
		if(!$value) return false;
		$regex = '/[0-9]{4}-[0-9]{2}-[0-9]{2}( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/';
		return preg_match($regex, $value);
	}
	
	public static function nombresMeses() {
		$meses = array();
		for($i=1;$i<=12;$i++)
			//$meses[$i] = date('F', mktime(0,0,0,$i,1));
			$meses[$i] = strftime('%B', mktime(0,0,0,$i,1));
		return $meses;
	}
}

?>
