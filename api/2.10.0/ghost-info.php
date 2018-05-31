<?php
require_once 'haut-api.php';

// Enregistre les fantômes et les votes envoyés par le serveur
// Données POST envoyées : ghostID
try {
	// Recherche du serveur
	$idServ = getIdServValide ( $_POST ["uuidServ"] );
	if (! $idServ) {
		erreurAPI ( "Unknown server" );
	}
	
	$reponse = $bdd->prepare ( "SELECT f.uuidMap, f.uuidJoueur, f.ticks, m.id idMap, m.etat = 'disponible' mapDispo FROM fantomes f INNER JOIN maps m ON f.uuidMap = m.uuid WHERE f.selecteur = :sel ORDER BY f.id DESC LIMIT 1" );
	$reponse->execute ( array (
			'sel' => $_POST ["ghostID"] 
	) ) or die ( "SQL error 16" );
	$donnees = $reponse->fetch ();
	$reponse->closeCursor ();
	
	if ($donnees) {
		ajouterReponse ( "trouve", true );
		ajouterReponse ( "uuidMap", $donnees ["uuidMap"] );
		ajouterReponse ( "uuidJoueur", $donnees ["uuidJoueur"] );
		ajouterReponse ( "ticks", $donnees ["ticks"] );
		ajouterReponse ( "idMap", $donnees ["mapDispo"] ? $donnees ["idMap"] : -1 );
	} else
		ajouterReponse ( "trouve", false );
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>