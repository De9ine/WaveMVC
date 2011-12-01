<?php
class EngineFactory {
	
	/**
	 *  Find engine type of $type i.e DisplayEngine or DBEngine
	 *  $type directly corresponds to a directory in the wave/mvc directory sturecture under
	 *  /lib/engines/
	 *  
	 *  When the type of engine is defined and found as part of the system, 
	 *  the factory goes on and tries to find the engine we are requesting 
	 *  for example if $type is DisplayEngine $engine might be PHPTheme
	 *  When it finds the PHPTheme.php file it returns a new PHPTheme object 
	 *  
	 *  Unless all these pre-conditions are true this method will throw a unexisting requested engine exception.
	 *  
	 */
	
	public static function create ($type, $engine) {
		// if found $type in /lib/engine 
			// Go on and find $engine 
			// If found
				// "before returning and exiting create function fire hook for "pre_engine_instance" with path to found
				// engine." 
				// return new object of kind $engine.
				// do nothing to object, do not run any initilization methods.
				 
	}
	
}