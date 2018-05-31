<?php
// Pour virer le connard de serveur f3797d26-efef-4893-b787-bd498f5f1a43
if (! strpos ( $_SERVER ['REQUEST_URI'], "stats" )) {
	echo json_encode ( [ 
			"STATUS" => "ERROR",
			"error reason" => "Your CreativeParkour version is too old, please update the plugin" 
	] );
	die ();
}

require_once '../2.2.2/haut-api.php';
?>