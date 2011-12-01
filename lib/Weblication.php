<?php 
include('Response/Endpoint.php');
/**
 * 
 * Base class for Weblication development,
 * this class is designed to be extended and not to be used as is.
 * 
 * It provides methods to handle 
 * @author krol
 *
 */

class Weblication {
	
	protected /* RequestHandler */ $request;
	protected /* ResponseHandler */ $response;
  protected	$manifest;
  protected $db;
  
  protected /* boolean */ $online;
  protected /* structure */ $pages;
  
  protected /* DisplayEngine */ $displayEngine;
  
	public function __construct(&$request){
		$this->request = $request;
		$this->request->initRequest();
		$this->db = &Kernel::$db;
		
		$this->initResponse();
	}
	/**
	 * Init this application. 
	 */
	
	public function init() {
		// Get endpoint for requested path
		$endpoint = &Kernel::getEndpoint($this->request->getPath());
		
		if($endpoint){
			include('web/'.$endpoint['callback'].'.php');
			$EndpointHandler = new $endpoint['callback']();
		} else {
			include('web/Error.php');
			$EndpointHandler = new Error();
			
		}
		
		if($EndpointHandler instanceof Endpoint) {
			$this->response->setEndpointResponse($EndpointHandler);
		} else {
			throw new Exception("Endpoint ".$endpoint['callback']." is not implementing Endpoint interface.");
		}
	}
	
	public function run() {
		return $this->response->renderResponse();
	}
	
	public function getDB () {
		return $this->db;
	}
	
	public function isOffline() {
		
	}
	
	
	
	
	protected function initResponse() {
		$this->response = new ResponseHandler();
	}
	
}	