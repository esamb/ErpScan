<?php

/**
 * Clase utilitaria que actúa como un repositorio de datos en formato de código real PHP,
 * principalmente usado para guardar arreglos relativamente complejos con listas y configuraciones
 * 
 * Los datos se guardan a través de un código el cual será el nombre de un archivo con extensión .php
 * y el cual se carga a la memoria a un caché interno.
 * 
 * La "magia" la realiza la función var_export() y opcionalmente puede codificar la información como
 * utf8 por si acaso
 * NOTA: no utiliza serialización
 *
 * @author vsayajin
 * @package components
 */
class PhpFilePersistence {
	
	/**
	 * @var array Cache interno de estructuras de datos
	 */
	protected $cache = array();
	/**
	 * Path de la carpeta, por defecto es el path físico del alias application.data de Yii
	 * @var string
	 */
	public $folder;
	/**
	 * Define si los datos se guardarán codificados como utf-8
	 * @var boolean
	 */
	public $utf8_encode = false;

	function __construct() {
		$this->folder = Yii::getPathOfAlias('application.data');
	}
	
	/**
	 * Obtiene datos relacionados con un código, primero prueba el caché interno y si no existe
	 * intenta cargarlo de disco
	 * @param string $codigo
	 * @return mixed
	 */
	public function getCodigo($codigo){
		if(!empty($this->cache[$codigo]))
			return $this->cache[$codigo];
		$data = $this->cargarArchivo($codigo);
		$this->cache[$codigo] = $data;
		return $data;
	}
	
	/**
	 * Intenta cargar un archivo PHP y lo devuelve la información si el archivo existe, null si no
	 * @param string $codigo
	 * @return mixed Datos incluidos
	 */
	public function cargarArchivo($codigo) {
		$path = $this->getFileName($codigo);
		if (file_exists($path))
			return include($path);
	}

	/**
	 * Guarda un archivo a disco, primero lo pone en el caché si no existe y luego lo guarda físicamente
	 * utilizando var_export
	 * @param string $codigo
	 * @param mixed $datos información
	 */
	public function persist($codigo, $datos) {
		$this->cache[$codigo] = $datos;
		$content = var_export($datos, true);
		if ($this->utf8_encode)
			$content = utf8_encode($content);
		$path = $this->getFileName($codigo);
		file_put_contents($path, "<?php\nreturn " . $content . ";\n");
	}
	
	/**
	 * Forma un nombre del archivo a partir de un código y la carpeta definida
	 * @param string $codigo
	 * @return string Path real del archivo
	 */
	public function getFileName($codigo){
		return $this->folder . '/' . $codigo . '.php';
	}
	
}

?>
