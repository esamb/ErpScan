<?php

class PHPTemplate {
    public $vars=array(); /// Holds all the template variables
    /**
     * Constructor
     *
     * @param $file string the file name you want to load
     */
    function PHPTemplate($file = '') {
        $this->file = $file;
    }

    /**
     * Set a template variable.
     */
    function set($name, $value) {
        $this->vars[$name] = $value;
    }

    /**
     * Open, parse, and return the template file.
     *
     * @param $file string the template file name
     */
    function fetch($_file = null) {
        if(!$_file) $_file = $this->file;

        extract($this->vars);          // Extract the vars to local namespace
        ob_start();                    // Start output buffering
		include($_file);                // Include the file
        $_contents = ob_get_contents(); // Get the contents of the buffer
        ob_end_clean();                // End buffering and discard
        return $_contents;              // Return the contents
    }

	function evalCode($_code){
		extract($this->vars);          // Extract the vars to local namespace
        ob_start();                    // Start output buffering
		eval('?> ' . $_code);
        $_contents = ob_get_contents(); // Get the contents of the buffer
        ob_end_clean();                // End buffering and discard
        return $_contents;  
	}
	
	function copyProps($other){
		$this->vars = $other->vars;	
	}
}

?>