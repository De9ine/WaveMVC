<?php

include('lib/kernel.php');
include('lib/Weblication.php');
include('lib/Request/RequestHandler.php');
include('lib/Response/ResponseHandler.php');

try {
	Kernel::bootstrap(Kernel::BOOTSTRAP_PHASE_FULL);
} catch(Exception $e){
	Kernel::$Weblication->isOffile(true);	
}
print "<pre>";
$output = Kernel::$Weblication->run();
print $output;
print "</pre>";