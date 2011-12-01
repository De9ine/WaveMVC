<?php

include('lib/DisplayEngine/DisplayEngine.php');

class PHPTheme extends DisplayEngine {
	
	private $vars;
	private $theme;
	
	public function setTheme($theme) {
	
		$this->theme = $theme;
		$this->initTheme();
	}
	
	
	/**
	 * 
	 * initialize theme from the manifest file defined in the themes/$this->theme/ menu structure.
	 * the themes manifest defines the predefined templatefiles
	 * hooks and other elements that needs to be found by the init function.
	 * this is so that we should never have to do recursive lookup on undefined menu structures.
	 * 
	 * When we have parsed the manifest.xml file we will have information about all the hooks this theme has implemented
	 * as well as all the template files used.
	 * 
	 * we will also have a register over all script files, and all the include files used to do operations.
	 * 
	 * The theme implements all hooks in a class named after $this->theme e.g Generic
	 * 
	 */
	private function initTheme() {
		// first we need to lookup the theme.
		kernel::processThemeManifest('themes/'.$this->theme.'/manifest.xml');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see DisplayEngine::display()
	 */
	public function display ($template = "") {
		
		$tFile = 'themes/'.$this->theme.'/templates/'.$template;
		if(is_file($tFile)){
			return eval("?>".file_get_contents($tFile)."<?");
		} else {
			eval ("?>".$template."<?");
		}
		
	}
	
	public function get ($variable) {
		return $this->vars[$variable];
	}
	
	public function set ($variable, $val) {
		$this->vars[$variable] = $val;
	}
	
	public function __set ($variable, $val) {
		$this->vars[$variable] = $val;
	}
	
	public function __get ($variable) {
		if(isset($this->vars[$variable])){
			return $this->vars[$variable];
		} else {
			throw new Exception("Theme has not defined variable: $variable");
		}
	}
	
}