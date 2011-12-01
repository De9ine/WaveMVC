<?php 

class Front extends BasicEndpoint {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function initEndpoint () {
		$this->responseTypes = array('text/html', 'application/json');
	}
	
	public function getData() {
		$request = Kernel::getRequest();
		$accept = $request->getPreferedAccept();
	}
	
	public function processResponse ($responseType) {
		switch ($responseType) {
			case 'text/html' :
				
				return $this->handleHTMLResponse();
				break;
			case 'application/json':
				
				break;
		}
	}
	
	private function handleHTMLResponse() {
		// Do pre render hook
		try {

			$response = array(
				
			);
			
			return  "<html><h1>p00p</h1></html>";
			
		}	catch (Exception $e) {
			print_r($e->getTraceAsString())	;
		}	
		
		
	}
	
}	