<?php 

interface Endpoint {
	public function initEndpoint();
	public function getData();
	public function setArgs();
	public function initResponse($contentType);
}

class BasicEndpoint implements Endpoint {
	
	protected $args = array();
	protected $responseTypes = array();
	protected $charset = array();
	
	public function __construct() {
		$this->initEndpoint();
	}
	
	public function initEndpoint() {
		
	} 
	
	public function getData() {
		
	}
	public function setArgs() {
		$args = func_get_args();
		$this->args = $args;
	}
	
	public function initResponse ($contentType) {
		if(in_array($contentType, $this->responseTypes) !== false){
			$this->processResponse($contentType);
		}
		else {
			Throw new Exception("Request accept type is not supported by this endpoint");
		}
	}
	
}