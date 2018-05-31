<?php
require_once 'haut-api.php';

// Signalement d'un fantôme
// Données POST envoyées : ipJoueur, uuidJoueur, nomJoueur, uuidMap
try {
	if (! getIdServValide ( $_POST ["uuidServ"] )) {
		// On n'accepte pas
		erreurAPI ( "unknown server", "true" );
	}
	
	// Recherche de l'ID d'utilisateur du mec
	$reponse = $bdd->prepare ( "SELECT id FROM utilisateurs WHERE minecraftUUID = :uuidJoueur" );
	$reponse->execute ( array (
			"uuidJoueur" => $_POST ["uuidJoueur"] 
	) ) or erreurAPI ( "SQL error 16" );
	$donnees = $reponse->fetch ();
	if ($donnees) {
		$idUtilisateur = $donnees ["id"];
	}
	$reponse->closeCursor ();
	
	// Recherche de l'ID de la map
	$reponse = $bdd->prepare ( "SELECT id FROM maps WHERE uuid = :uuidMap" );
	$reponse->execute ( array (
			"uuidMap" => $_POST ["uuidMap"] 
	) ) or erreurAPI ( "SQL error 27" );
	$donnees = $reponse->fetch ();
	if ($donnees) {
		$idMap = $donnees ["id"];
	}
	$reponse->closeCursor ();
	
	if (! $idMap) {
		erreurAPI ( "Unknown map" );
		repondre ();
	} else {
		
		$idMAJ = 0;
		$requete = "INSERT INTO signalementsFantomes (cleID, idUtilisateur, ipJoueur, idMap, date, uuidServ, etat) VALUES (:cleID, :idUtilisateur, :ipJoueur, :idMap, NOW(), :uuidServ, 'attente')";
		if ($idUtilisateur) {
			// Recherche de si le mec a déjà commencé un signalement pour cette map pour pas en mettre 2
			$reponse = $bdd->prepare ( "SELECT id FROM signalementsFantomes WHERE idUtilisateur = :idUtilisateur AND idMap = :idMap AND etat = 'attente'" );
			$reponse->execute ( array (
					"idMap" => $idMap,
					"idUtilisateur" => $idUtilisateur 
			) ) or erreurAPI ( "SQL error 47" );
			$donnees = $reponse->fetch ();
			$reponse->closeCursor ();
			if ($donnees) {
				$idMAJ = $donnees ["id"];
				$requete = "UPDATE signalementsFantomes SET cleID = :cleID, ipJoueur = :ipJoueur, date = NOW(), uuidServ = :uuidServ, etat = 'attente' WHERE id = :id AND idMap = :idMap AND idUtilisateur = :idUtilisateur";
			}
		}
		
		// Création et récupération de la clé d'accès
		$cle = nouvelleCle ( $_POST ["ipJoueur"], $_POST ["uuidJoueur"], $_POST ["nomJoueur"] );
		
		// Enregistrement du vote dans la table
		$array = array (
				"cleID" => $cle->id,
				"idUtilisateur" => $idUtilisateur ? $idUtilisateur : - 1,
				"ipJoueur" => $_POST ["ipJoueur"],
				"idMap" => $idMap,
				"uuidServ" => $_POST ["uuidServ"] 
		);
		if ($idMAJ)
			$array ["id"] = $idMAJ;
		$req = $bdd->prepare ( $requete );
		$req->execute ( $array ) or die ( "SQL error 67" );
		$req->closeCursor ();
		
		ajouterReponse ( "cle", $cle->cle );
	}
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>