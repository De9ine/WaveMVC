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
	public static $bootstrap;
	
	public static $endpoints = array();
	
	const BOOTSTRAP_PHASE_FULL = 0;
	const BOOTSTRAP_PHASE_MANIFEST = 1;
	const BOOTSTRAP_PHASE_INDEX_ENDPOINT = 2;
	const BOOTSTRAP_INDEX_PLUGINS	= 3;
	const BOOTSTRAP_PHASE_INIT_HANDLER = 4;
	const BOOTSTRAP_PHASE_CLEAN_ENDPOINT_INDEX = 5;
	
	
	
	
	
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
				self::$bootstrap = true;
				include('lib/IO/DB/DB.php');
			case Kernel::BOOTSTRAP_PHASE_MANIFEST :
				self::$bootstrap = true;
				try {
					self::$manifest = Kernel::processSiteManifest();
				} catch (Exception $e){
					print_r($e->getTraceAsString());
				}
			case Kernel::BOOTSTRAP_PHASE_INDEX_PLUGINS :
				self::$bootstrap = true;
				Kernel::indexPlugins();
				
			case Kernel::BOOTSTRAP_PHASE_INIT_HANDLER : 
				self::$bootstrap = true;
				include(Kernel::$manifest['classpath']);
				
				self::$RequestHandler = new RequestHandler();
				self::$Weblication = new Main(self::$RequestHandler);
				self::$Weblication->init();
				
		 
				// Index all hook callbacks
				
				
			break;
				
			case Kernel::BOOTSTRAP_PHASE_CLEAN_ENDPOINT_INDEX : 
				self::$bootstrap = true;
			break;
			
			default: 
				self::$bootstrap = false;
				throw new Exception("Boostrap failed unrecognized bootstrap phase: ".$phase);
		}
		self::$bootstrap = false;
		
	}
	
	public static function processThemeManifest($manifestPath) {
		$themeManifest 	= array();
	
		$manifest 			= new DOMDocument();
		$manifest->loadXML(file_get_contents($manifestPath));
		$manifestRoot		= $manifest->getElementsByTagName('theme')->item(0);
		
		$themeManifest['name']				= $manifestRoot->getElementsByTagName('name')->item(0)->nodeValue;
		$themeManifest['class']				= $manifestRoot->getElementsByTagName('class')->item(0)->nodeValue;
		$themeManifest['description']	= $manifestRoot->getElementsByTagName('description')->item(0)->nodeValue;
		$themeHooks										= $manifestRoot->getElementsByTagName('hooks')->item(0);
		
		
		
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
	
	public static function processSiteManifest() {
		
		$siteManifest 		= array();
		$manifest 				= new DOMDocument;
		$manifest->loadXML(file_get_contents('manifest.xml'));
		// set the manifestRoot node.
		$manifestRoot			= $manifest->getElementsByTagName('site')->item(0);
		
		// siteManifest variables. 
		/*
		 * Maybe siteManifest and $manifest should be objects of a class called Manifest?
		 * Need to look in to this ona separete occasion.
		 * @TODO Create Manifest class for Kernel object.
		 */
		$siteManifest['name'] 			= $manifestRoot->getElementsByTagName('name')->item(0)->nodeValue;
		$siteManifest['classpath'] 	= $manifestRoot->getElementsByTagName('classpath')->item(0)->nodeValue;
		$siteManifest['pluginpath']	= $manifestRoot->getElementsByTagname('pluginpath')->item(0)->nodeValue;
		$siteManifest['theme']			= $manifestRoot->getElementsByTagName('theme')->item(0)->nodeValue;
		
		$endpointsRoot 	= $manifestRoot->getElementsByTagName('endpoints')->item(0);
		
		$endpoints 			= array();
		
		foreach($endpointsRoot->getElementsByTagName('endpoint') as $endpointNode){
			$endpoints[] = array(
				'path' 			=> $endpointNode->getAttribute('path'), 
				'callback' 	=> $endpointNode->getAttribute('callback'),
			);
		}
		
		self::$endpoints = array_merge(self::$endpoints, $endpoints);
		
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
	 * 
	 * First check parameter $path.. if not ""
	 *   then foreach registrered endpoints
	 *   if that endpoints devided path has equal number of steps as $path, continue
	 *     if (endpoint['path'][i] EQ $path[i] OR endpoint['path'][i] == %) 
	 *       we have created wildcard path steps using % and made sure that all steps in the path is
	 *       tested for validation
	 * 
	 * @TODO The outer loop of this algorithm needs to be revisited and changed for optimisation go over 
	 * all endpoints is useless, we need to create a smarter subset to test against.
	 *  
	 * 
	 */
	
	public static function getEndpoint($requestedPath = array("/")){
		if($requestedPath != ""){
			foreach(self::$endpoints as $index => $endpoint){
				// Need to handle "/" root path differently
				// For it to never fail we need to check this first.
				if($endpoint['path'] != "/"){
					$endpointPathSteps = explode("/", $endpoint['path']);
				} else {
					$endpointPathSteps = array("/");
				}
				$pathIsCorrect = false;
				
		  	if(count($endpointPathSteps) == count($requestedPath)) {
		  	  foreach($endpointPathSteps as $i => $step){
						 if($endpointPathSteps[$i] == $requestedPath[$i] || $endpointPathSteps[$i] == "%"){
							 $pathIsCorrect = true;
						 }
		      }
				}
		
				if ($pathIsCorrect) {
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
	
	/**
	 * 
	 * When looking for plugins we first go through core/ directory.
	 * Then we go through $manifest['pluginpath'] directory.
	 * What we are looking for is <pluginpath>/<plugin>/manifest.xml and Plugin.php
	 * 
	 * When a manifest file is found we start an initilization of the plugin by running the plugins public method
	 * index
	 * 
	 * this method is supposed to process some initial processes within the plugin, for example it's possible to directly in this method
	 * do some database modifications, like altering the weight of the plugins order of execution.
	 * 
	 */
	
	public static function indexPlugins() {
		
		if(self::$bootstrap){
			$corePath = "core";
			$pluginPath = self::$manifest['pluginpath'];
			
			$plugins = array();
			// 1 go through and see if we find anything within the corepath.
			$plugins = array_merge($plugins, self::_findPluginsInDir($corePath));
	
			
			// 2 go through and see if we find anything within the pluginpath.
			$plugins = array_merge($plugins, self::_findPLuginsInDir($pluginPath));
			
			
		
		} else {
			throw new Exception("Method indexPlugins cannot be run outside of a bootstrapping sequence");
		}
	}
	
	public static function processPluginManifest ($manifest) {
		
	}
	
	public static function _findPluginsInDir($dir){
		
		$plugins = array();
		
		if(is_dir($dir)){
			$dirObj = dir($dir);
			while ($plugin = $dirObj->read()) {
				if($plugin != '..' && $plugin != '.'){
					if(is_dir($dir."/".$plugin)){
						if (is_file($dir."/".$plugin."/manifest.xml")) {
							$plugins[] = array('path' => $dir."/".$plugin."/");
						}
					}
				}
			}
		}
		return $plugins;
	}
	
}