<?php
$authConfig = $_SERVER ['DOCUMENT_ROOT'] . '/hybridauth/config.php';
require_once ($_SERVER ['DOCUMENT_ROOT'] . '/hybridauth/Hybrid/Auth.php');
try {
	$hybridauth = new Hybrid_Auth ( $authConfig );
} catch (Exception $e) {
	$_SESSION["erreurs"][] = "Authentication failed.";
	header("Location: login.php");
}


// TODO Compléter
?>