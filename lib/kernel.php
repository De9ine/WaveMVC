<?php 
/**
 * @author krol
 *
 * The kernel is a class with a set of static methods to handle all the major parts of the system
 * It's repsonsible for providing a easy to fetch framework for all objects represented in the mvc
 * 
 * The kernel is part of the <MVC> and is licensed under the same general license.
 * the code in this source file is free to use in any project, commercial or private.
 * 
 *
 */

class Kernel {
	
	public static $baseurl;
	public static $RequestHandler;
	public static $Weblication;
	public static $manifest;
	public static $db;
	
	public static $endpoints;
	
	const BOOTSTRAP_PHASE_FULL = 0;
	const BOOTSTRAP_PHASE_MANIFEST = 1;
	const BOOTSTRAP_PHASE_INDEX_ENDPOINT = 2;
	const BOOTSTRAP_PHASE_INIT_HANDLER = 3;
	const BOOTSTRAP_PHASE_CLEAN_ENDPOINT_INDEX = 4;
	
	
	
	
	
	/**
	 * 
	 * Handle initial manifestparsing
	 * Object instanciation and initilization.
	 * 
	 * The $Weblication object is responsible for handling all plugins and other logics involving 
	 * the actual application framework. The kernel only provides a logical tie between the framework.
	 * @see /lib/Weblication.php
	 * 
	 * 
	 * $RequestHandler object is responsible for providing a structured representation of the request from the user.
	 * @see /lib/Request/RequestHandler.php
	 * 
	 */
	public static function bootstrap ($phase) {
		
		switch ($phase) {
			
			case Kernel::BOOTSTRAP_PHASE_FULL :
				include('lib/IO/DB/DB.php');
			case Kernel::BOOTSTRAP_PHASE_MANIFEST :
				try {
					self::$manifest = Kernel::process_manifest();
				} catch (Exception $e){
					print_r($e->getTraceAsString());
				}
			case Kernel::BOOTSTRAP_PHASE_INIT_HANDLER : 
				include(Kernel::$manifest['classpath']);
				
				self::$RequestHandler = new RequestHandler();
				self::$Weblication = new Main(self::$RequestHandler);
				self::$Weblication->init();
		 
			break;
				
			case Kernel::BOOTSTRAP_PHASE_CLEAN_ENDPOINT_INDEX : 
			
			break;
			
				throw new Exception("Boostrap failed unrecognized bootstrap phase: ".$phase);
			
			default: 
		}
		
		
	}
	
	/**
	 * 
	 * This function parses the /manifest.xml to determain some initial information about the current
	 * application.
	 * 
	 * It's dependant of the PHP DOM library.
	 * 
	 * @throws Exception
	 */
	
	public static function process_manifest() {
		
		$siteManifest 		= array();
		$manifest 				= new DOMDocument;
		$manifest->loadXML(file_get_contents('manifest.xml'));
		// set the manifestRoot node.
		$manifestRoot					 			= $manifest->getElementsByTagName('site')->item(0);
		
		// siteManifest variables. 
		/*
		 * Maybe siteManifest and $manifest should be objects of a class called Manifest?
		 * Need to look in to this ona separete occasion.
		 * @TODO Create Manifest class for Kernel object.
		 */
		$siteManifest['name'] 			= $manifestRoot->getElementsByTagName('name')->item(0)->nodeValue;
		$siteManifest['classpath'] 	= $manifestRoot->getElementsByTagName('classpath')->item(0)->nodeValue;
		$siteManifest['theme']			= $manifestRoot->getElementsByTagName('theme')->item(0)->nodeValue;
		
		$endpointsRoot 	= $manifestRoot->getElementsByTagName('endpoints')->item(0);
		
		$endpoints 			= array();
		
		foreach($endpointsRoot->getElementsByTagName('endpoint') as $endpointNode){
			$endpoints[] = array(
				'path' 			=> $endpointNode->getAttribute('path'), 
				'callback' 	=> $endpointNode->getAttribute('callback'),
			);
		}
		
		self::$endpoints = $endpoints;
		$db 									= $manifestRoot->getElementsByTagName('db')->item(0);
		$dbInfo 							= array();
		$dbInfo['user'] 			= $db->getElementsByTagName('user')->item(0)->nodeValue;
		$dbInfo['passwd'] 		= $db->getElementsByTagName('passwd')->item(0)->nodeValue;
		$dbInfo['host'] 			= $db->getElementsByTagName('host')->item(0)->nodeValue;
		$dbInfo['database'] 	= $db->getElementsByTagName('database')->item(0)->nodeValue;
		$dbInfo['handler'] 		= $db->getElementsByTagName('handler')->item(0)->nodeValue;
		
		
		self::$db = new DB($dbInfo['user'], $dbInfo['passwd'], $dbInfo['host']);
		self::$db->setDatabase($dbInfo['database']);
		self::$db->setDataLayer($dbInfo);

		
		
		if(!$manifest->validate()){
			throw new Exception("Manifest not valid.");
		}
		
		return $siteManifest;
		
	}
	
	public static function handle_db () {
		
	}
	
	/**
	 * Get endpoint for a given path
	 * @param String $path
	 */
	
	public static function getEndpoint($path = "/"){
		if($path != ""){
			foreach(self::$endpoints as $index => $endpoint){
				if($endpoint['path'] == $path){
					return $endpoint;
				}
			}
		}
	}
	
	public static function registerHookCallback ($hook, $callback) {
		$callback = serialize($callback);
		
		Kernel::$db->query("INSERT INTO hook_handlers(hook, callback) VALUES ('%s', '%s')", $hook, $callback);
	}
	
	/**
	 * 
	 * This method 
	 * @param unknown_type $hook
	 */
	
	public static function fireHook($hook) {
		
		$args = func_get_args();
		$args = array_shift($args);
		
		print_r($hook." fired with args" .$args);
		
		while($callbacks = Kernel::$db->getArrayResult(Kernel::$db->query("SELECT callback FROM hook_handlers WHERE hook = '%s'", $hook))) {
				$callback = unserialize($callbacks['callbak']);
				
				return call_user_func_array($callback, $args);
		}
		
	}
	
	public static function getRequest() {
		return self::$RequestHandler;
	}
}