<?php 
include('Response/Endpoint.php');
/**
 * 
 * Base class for Weblication development,
 * this class is designed to extended and not to be used as is.
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
		// Register all endpoints
		$endpoint = &Kernel::getEndpoint($this->request->getPath());
		
		if($endpoint){
			include('web/'.$endpoint['callback'].'.php');
			$EndpointHandler = new $endpoint['callback']();
		} else {
			
			include('web/Error.php');
			$EndpointHandler =new Error();
			
		}
		call_user_func_array(array($EndpointHandler, 'setArgs'), array("Herp", "Di", "Derp"));
		
		$this->response->setEndpointResponse($EndpointHandler);
	}
	
	public function renderResponse() {
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