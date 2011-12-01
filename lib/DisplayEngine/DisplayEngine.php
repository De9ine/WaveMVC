<?php
abstract class DisplayEngine {
	
	/**
	 * 
	 * Implementation of display defines how response is displayed to the user.
	 * @param unknown_type $template
	 */
	public abstract function display($template = "");
	
	public abstract function __get($variable);
	public abstract function __set($variable, $val);
	public abstract function get($variable);
	public abstract function set($variable, $val);
	
}