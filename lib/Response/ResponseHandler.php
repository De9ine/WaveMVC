<?php 

class ResponseHandler {
	
	protected $endpoint;
	
	public function __construct() {
			
	}
	
	public function processResponse() {
		$data = $this->endpoint->getData();
		$preferedContentType = Kernel::getRequest()->getPreferedAccept();
		return $this->endpoint->processResponse($preferedContentType['accept']);
	}
	
	public function setEndpointResponse($endpoint) {
		$this->endpoint = $endpoint;
	}
}
