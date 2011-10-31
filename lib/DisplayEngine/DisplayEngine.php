<?php
abstract class DisplayEngine {
	
	public abstract function display($template);
	
	public abstract function __get($variable);
	public abstract function __set($variable, $val);
	public abstract function get($variable);
	public abstract function set($variable, $val);
	
}