<?php
require_once '../includes/haut-global.php';

if ($_GET ["token"]) {
	// Recherche du mail correspondant au jeton
	$reponse = $bdd->query ( "SELECT id, idUtilisateur, verif FROM mails WHERE verif <> '' ORDER BY id DESC" ) or die ( "SQL error 6" );
	while ( ! $id && $donnees = $reponse->fetch () ) {
		if (password_verify ( $_GET ["token"], $donnees ["verif"] )) {
			$id = $donnees ["id"];
			$idUtilisateur = $donnees ["idUtilisateur"];
		}
	}
	$reponse->closeCursor ();
	
	if (! $id || ! $idUtilisateur) {
		$_SESSION ["erreurs"] [] = "Your request is not valid or this email is already verified.";
	} else {
		// Les autres adresses ne sont ples principales
		$req = $bdd->prepare ( "UPDATE mails SET principale = 0 WHERE idUtilisateur = :id" );
		$req->execute ( array (
				'id' => $idUtilisateur 
		) ) or die ( "SQL error 22" );
		$req->closeCursor ();
		// Validation du mail
		$req = $bdd->prepare ( "UPDATE mails SET verif = '', principale = 1 WHERE id = :id" );
		$req->execute ( array (
				'id' => $id 
		) ) or die ( "SQL error 28" );
		$req->closeCursor ();
		$_SESSION ["msgOK"] [] = "Your email has been verified!";
	}
}
retournerOuHeader ( "index.php" );
?>