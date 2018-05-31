<?php
require_once '../includes/haut-global.php';
verifLogin ();

if ($_GET ["servUUID"] && $_GET ["page"]) {
	$reponse = $bdd->prepare ( "SELECT id, idsProprietaires FROM serveurs WHERE uuid = :uuid" );
	$reponse->execute ( array (
			'uuid' => $_GET ["servUUID"] 
	) ) or die ( "SQL error 9" );
	$infosServ = $reponse->fetch ();
	$reponse->closeCursor ();
	
	if (! $infosServ || ! in_array ( $_SESSION ["utilisateur"]->id, explode ( ";", $infosServ ["idsProprietaires"] ) )) {
		http_response_code ( 403 );
		$_SESSION ["erreurs"] [] = "You are not allowed to access this page."; // TODO Dire que l'admin peut ajouter le mec aux propriétaires
		header ( "Location: index.php" );
		die ( "Redirecting..." );
	} else {
		header ( "Location: .." . $_GET ["page"] . $infosServ ["id"] );
		die ( "Redirecting..." );
	}
}
header ( "Location: index.php" );
?>