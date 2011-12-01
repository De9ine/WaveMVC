<?php 
include('lib/EngineFactory.php');
class Main extends Weblication {
	
	private $themeEngine;

	public function __construct (&$request) {
		parent::__construct($request);
	}
		
	/**
	 * (non-PHPdoc)
	 * @see Weblication::init()
	 */
	
	public function init () {
		parent::init();
		
		/**
		 * 
		 */
		
		$this->themeEngine = EngineFactory::create("DisplayEngine", "PHPTheme");
		
	}
	
	public function run () {
		return $this->response->processResponse();
		
	}
	
	public function isOffline () {
		
	}
	
	
}