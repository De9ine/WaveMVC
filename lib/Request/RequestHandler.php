<?php
include('lib/Session/Session.php');
include('lib/Session/User.php');

/**
 * @author krol
 * The RequestHandler class parses and handles the HTTP request and provides methods to make logical decisions based
 * on the request that the webserver is actually recieving instead of blindy assuming that the client is requesting
 * a pure XHTML webpage developers are able to take action according to all properties of the HTTP request.
 * 
 * For example and Endpoint implemenetation may take care of providing a response object for XML, JSON, XHTML, PHP and what ever.
 * This way of handeling requests is based on the idea that each page on the site may function as a Service Endpoint.
 * 
 * This idea was adopted because there was an initial need to make a site that was first developed with this system needed to provide 
 * JSON response and all pages needed to be able to repsonde in JSON or XHTML
 * 
 */

class RequestHandler {
	
	private $isFront = false;
	private $path;
	private $user;
	private $session;
	private $client;
	
	private $accept;
	private $acceptEncoding;
	private $acceptLanguage;
	private $acceptCharset;
	
	public function __construct () {
		
	} 
	
	public function initRequest() {
		$this->translateRequestPath();
		$this->determainIfFront();
		
		$this->user = new User();
		
		$this->accept 					= $this->parseAccept();
		$this->acceptCharset 		= $this->parseAcceptCharset();
		$this->acceptLanguage 	= $this->parseAcceptLanguage();
		$this->acceptEncoding 	= $this->parseAcceptEncoding();
	}
	
	public function isFront() {
		return $isFront;
	}
	
	public function getPath() {
		return $this->path;
	}
	
	public function getAccept () {
		return $this->accept;
	}
	
	public function getPreferedAccept () {
		
		// First determain if any accept has parameter level set to 1,
		foreach($this->accept as $accept){
			if(isset($accept['params'])){
				
				if(isset($accept['params']['level']) && $accept['params']['level'] == '1'){
					return $accept;
				}
			}
		}
		// else return accept [0].
		return $this->accept[0];
		
	}
	
	protected function translateRequestPath () {
		
		if(isset($_GET['q'])) {
			$this->path = explode("/",$_GET['q']);
		}
		if($this->path == ""){
			$this->path = array("/");
		}
		
	}
	
	protected function determainIfFront() {
		if($this->path[0] == ""){
			$this->isFront = true;
		} 
	}
	
	protected function parseAccept () {
		
		$accept = $_SERVER['HTTP_ACCEPT'];
		$accept = explode(',', $accept);
		
		$tAccept = array();
		foreach($accept as $a){
			$acceptStrct = explode(";", $a);
			if(count($acceptStrct) == 1){
				$tAccept[] = array('accept' => $acceptStrct[0]);
			} else {
				$acceptStr = $acceptStrct[0];
				unset($acceptStrct[0]);
				$params = array();
				foreach($acceptStrct as $strct){
					$q = explode("=", $strct);
					
					$params[$strct[0]] = $q[1];
				}
				
				$tAccept[] = array('accept' => $acceptStr, 'params' => $params);
				
			}
		}
		
		$accept = $tAccept;
		
		return $accept;
	}
	
	protected function parseAcceptCharset () {
		return $_SERVER['HTTP_ACCEPT_CHARSET'];
	}
	
	protected function parseAcceptLanguage () {
		return $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	}
	
	protected function parseAcceptEncoding () {
		return $_SERVER['HTTP_ACCEPT_ENCODING'];
	}
	
}