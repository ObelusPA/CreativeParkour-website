<?php
require_once 'haut-api.php';

// Téléchargement d'une map
// Données POST envoyées : ipJoueur, uuidJoueur, idMap
try {
	
	// Recherche du serveur
	$idServ = getIdServValide ( $_POST ["uuidServ"] );
	if (! $idServ) {
		ajouterReponse ( "servInconnu", "true" );
		repondre ();
	}
	
	preg_match ( "#\?id=(\d+)#", $_POST ["idMap"], $matches );
	if ($matches [1])
		$idMap = ( int ) $matches [1];
	else
		$idMap = ( int ) $_POST ["idMap"];
		
		// Récupération des données concernant la map
	$reponse = $bdd->prepare ( "SELECT id, nom, uuid, createur, contributeurs, taille, fichierContenu, difficulte FROM maps WHERE id = :idMap AND etat = 'disponible'" );
	$reponse->execute ( array (
			"idMap" => $idMap 
	) ) or erreurAPI ( "SQL error 25" );
	$donnees = $reponse->fetch ();
	$reponse->closeCursor ();
	if ($donnees) {
		$idMap = $donnees ["id"];
		ajouterReponse ( "nomMap", $donnees ["nom"] );
		ajouterReponse ( "uuidMap", $donnees ["uuid"] );
		ajouterReponse ( "createur", $donnees ["createur"] );
		ajouterReponse ( "contributeurs", explode ( ";", $donnees ["contributeurs"] ) );
		ajouterReponse ( "difficulte", $donnees ["difficulte"] );
		ajouterReponse ( "taille", $donnees ["taille"] );
		$buffer = "";
		$zp = gzopen ( "../../maps data/" . $donnees ["fichierContenu"] . ".gz", 'r' );
		if (is_bool ( $zp ))
			erreurAPI ( "GZ error" );
		while ( ! gzeof ( $zp ) ) {
			$buffer .= gzread ( $zp, 4096 );
		}
		gzclose ( $zp );
		ajouterReponse ( "contenu", json_decode ( $buffer ) );
	} else {
		erreurAPI ( "unknown map" );
	}
	
	// Enregistrement du téléchargement
	$reponse = $bdd->prepare ( "SELECT id FROM telechargements WHERE idServ = :idServ" );
	$reponse->execute ( array (
			"idServ" => $idServ 
	) ) or erreurAPI ( "SQL error 53" );
	$donnees = $reponse->fetch ();
	$reponse->closeCursor ();
	if (! $donnees) {
		$req = $bdd->prepare ( "INSERT INTO telechargements SET date = NOW(), idMap = :idMap, idServ = :idServ, telechargeurUUID = :telechargeurUUID" );
		$req->execute ( array (
				"idMap" => $idMap,
				"idServ" => $idServ,
				"telechargeurUUID" => $_POST ["uuidJoueur"] 
		) ) or erreurAPI ( "SQL error 56" );
		$req->closeCursor ();
		calculerTelechargements ( $idMap );
	}
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>