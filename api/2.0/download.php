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
	
	// Récupération des données concernant la map
	$reponse = $bdd->prepare ( "SELECT id, nom, uuid, createur, contributeurs, taille, fichierContenu, difficulte FROM maps WHERE id = :idMap AND etat = 'disponible'" );
	$reponse->execute ( array (
			"idMap" => $_POST ["idMap"] 
	) ) or die ( "SQL error 19" );
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
		ajouterReponse ( "contenu", $buffer );
	} else {
		erreurAPI ( "unknown map" );
	}
	
	// Enregistrement du téléchargement
	$reponse = $bdd->prepare ( "SELECT id FROM telechargements WHERE idServ = :idServ" );
	$reponse->execute ( array (
			"idServ" => $idServ 
	) ) or die ( "SQL error 47" );
	$donnees = $reponse->fetch ();
	$reponse->closeCursor ();
	if (! $donnees) {
		$req = $bdd->prepare ( "INSERT INTO telechargements SET date = NOW(), idMap = :idMap, idServ = :idServ, telechargeurUUID = :telechargeurUUID" );
		$req->execute ( array (
				"idMap" => $idMap,
				"idServ" => $idServ,
				"telechargeurUUID" => $_POST ["uuidJoueur"] 
		) ) or die ( "SQL error 56" );
		$req->closeCursor ();
		calculerTelechargements ( $idMap );
	}
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>