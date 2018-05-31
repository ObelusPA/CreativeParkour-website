<?php
require_once 'haut-api.php';

// Inscription sur le site
// Données POST envoyées : ipJoueur, uuidJoueur, nomJoueur
try {
	// Recherche de si le mec est déjà inscrit
	$reponse = $bdd->prepare ( "SELECT id FROM utilisateurs WHERE minecraftUUID = :uuidJoueur" );
	$reponse->execute ( array (
			"uuidJoueur" => $_POST ["uuidJoueur"] 
	) ) or erreurAPI ( "SQL error 11" );
	$donnees = $reponse->fetch ();
	$reponse->closeCursor ();
	if ($donnees) {
		ajouterReponse ( "dejaInscrit", true );
		repondre ();
	} else {
		$cle = nouvelleCle ( $_POST ["ipJoueur"], $_POST ["uuidJoueur"], $_POST ["nomJoueur"] );
		
		ajouterReponse ( "cle", $cle->cle );
	}
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>