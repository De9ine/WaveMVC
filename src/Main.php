<?php 

include('lib/DisplayEngine/PHPTheme.php');

class Main extends Weblication {

	public function __construct (&$request) {
		parent::__construct($request);
	}
		
	/**
	 * (non-PHPdoc)
	 * @see Weblication::init()
	 */
	
	public function init () {
		parent::init();
		$this->displayEngine = new PHPTheme();
	}
	
	public function run () {
		
		$this->displayEngine->pewp = "oooiih";
		$this->displayEngine->header = "apa";
		
		$this->displayEngine->setTheme(Kernel::$manifest['theme']);
		
		print_r($this->displayEngine->display('themes/generic/Page.tpl.php'));
		
	}
	
	public function isOffline () {
		
	}
	
	
}