<?php 

class Front extends Endpoint {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function initEndpoint () {
		$this->contentTypes = array('text/html', 'application/json');
	}
	
	public function getData() {
		$request = Kernel::getRequest();
		$accept = $request->getPreferedAccept();
	}
	
	public function processResponse ($returnType) {
		switch ($returnType) {
			case 'text/html' :
				
				$this->handleHTMLResponse();
				break;
			case 'application/json':
				
				break;
		}
	}
	
	private function handleHTMLResponse() {
		// Do pre render hook
		try {

			
			
		}	catch (Exception $e) {
			print_r($e->getTraceAsString())	;
		}	
		
		
	}
	
	public function setDisplayEngine($displayEngine) {
		$this->displayEngine = $displayEngine;
	}
	
	public function getDisplayEngine() {
		if(isset($this->displayEngine)){
			return $this->displayEngine;
		} else {
			Throw new Exception('A theme engine is not defined for this application.');
		}
	
	}
	
}	