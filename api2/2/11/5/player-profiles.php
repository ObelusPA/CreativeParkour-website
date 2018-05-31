<?php
require_once 'haut-api.php';

// Renvoie les données du profil des joueurs listés dans uuids
// Données POST envoyées : uuids
try {
	$joueurs = explode ( ";", $_POST ["uuids"] );
	
	foreach ( $joueurs as $uuid ) {
		$profil = getProfilMC ( $uuid );
		$liste [] = array (
				"uuid" => $uuid,
				"textures" => $profil ["textures"],
				"signature" => $profil ["signature"] 
		);
	}
	
	ajouterReponse ( "profils", $liste );
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>