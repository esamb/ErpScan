<?php

/**
 * Utilitarios para la gestión y formato de archivos
 *
 * @author vsayajin
 * @package components
 */
class FileUtils {

	/**
	 * Toma un archivo del sistema y lo envia a la salida estándar utilizando streams para mayor
	 * eficiencia
	 * @param string $filename Ruta física del archivo
	 * @param string $outname Nombre lógico del archivo
	 * @param sring $type Tipo MIME del archivo
	 * @return boolean 
	 */
	public static function streamFile($filename, $outname, $type) {
		//Yii::app()->getRequest()->sendFile($file->nombre, file_get_contents($path), $file->tipo_mime, true);
		$size = filesize($filename);
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		if ($type)
			header("Content-type: $type");
		if (ini_get("output_handler") == '')
			header('Content-Length: ' . $size);
		header("Content-Disposition: attachment; filename=\"$outname\"");
		header('Content-Transfer-Encoding: binary');

		$fp = fopen('php://output', 'w+');
		fwrite($fp, file_get_contents($filename));
		fclose($fp);
		return true;
		// otro método streaming agresivo, se deberia hacer sensible al tamaño del archivo
		$ff = fopen($filename, 'r');
		$buff = 32768;
		while (!feof($ff)) {
			$bytes = fread($ff, $buff);
			fwrite($fp, $bytes);
		}
		fclose($ff);
		fclose($fp);
	}

	/**
	 * Envía el contenido de la variable $content al cliente web
 	 * @param string $content Bytes para enviar al cliente
	 * @param string $outname Nombre lógico del archivo
	 * @param sring $type Tipo MIME del archivo
	 */
	public static function streamBytes($content, $outname, $type) {
		$size = strlen($content);
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		if ($type)
			header("Content-type: $type");
		if (ini_get("output_handler") == '')
			header('Content-Length: ' . $size);
		header("Content-Disposition: attachment; filename=\"$outname\"");
		header('Content-Transfer-Encoding: binary');

		$fp = fopen('php://output', 'w+');
		fwrite($fp, $content);
		fclose($fp);
		return true;
	}
	
	/**
	 * Devuelve un string formateado como tamaño de archivo con los sufijos
	 * "bytes" o "KB" dependiendo del tamaño del valor pasado  
	 * @param number $long
	 */
	public static function formatTamanio($long) {
		if (!$long)
			return '';
		$msj = $long . ' bytes';
		if ($long > 1024)
			return (int) ($long / 1024) . ' KB';
		return $msj;
	}
	
	/**
	 * Trata de conectarse a un url específico y toma el tiempo de respuesta, como hacer ping. 
	 * Si el url no es accesible, retorna -1
	 * @param string $url
	 * @return number Tiempo de respuesta o -1 si el servicio está inactivo
	 */
	public static function pingURL($url) {
		$starttime = microtime(true);
		//$file = fsockopen($domain, 80, $errno, $errstr, 10);
		$file = fopen($url, 'r');
		$stoptime = microtime(true);
		$status = 0;

		if (!$file)
			$status = -1;  // Site is down
		else {
			fclose($file);
			$status = ($stoptime - $starttime) * 1000;
			$status = floor($status);
		}
		return $status;
	}
	
}

?>
