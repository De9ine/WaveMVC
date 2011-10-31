<?php 
class Endpoint {
	
	protected $args = array();
	protected $contentTypes = array();
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
		if(in_array($contentType, $this->contentTypes) !== false){
			$this->processResponse($contentType);
		}
	}
	
}