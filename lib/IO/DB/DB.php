<?php

class DB {
	
	private $handler;
	private $database;
	private $user;
	private $passwd;
	private $host;
	
	
	public function __construct ($user, $passwd, $host) {
		$this->handler = mysql_connect($host, $user, $passwd);
	}
	
	public function setDatabase($db) {
		mysql_select_db($db, $this->handler);
	}
	
	public function setDataLayer($datalayer) {
		
	}
	
	public function query($query) {
		
		$args = func_get_args();
		array_shift($args);
				
		foreach($args as $index => $arg){
			$args[$index] = $this->dbEscapeString($arg);
		}

		$query = 	call_user_func_array('sprintf', array_merge((array)$query,$args));
		return mysql_query($query, $this->handler);
		
	}
	
	public function result ($result) {
		var_dump($result);
		return mysql_result($result);
	}
	
	public function getArrayResult($result){
		return mysql_fetch_array($result, MYSQL_ASSOC);
	}
	
	private function dbEscapeString($arg) {
		return mysql_real_escape_string((string)$arg, $this->handler);
	}
}