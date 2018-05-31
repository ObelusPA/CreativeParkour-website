<?php
require_once 'haut-api.php';

// Enregistrement d'un serveur
// Données POST envoyées : ipServ, nomServ, ipJoueur, uuidJoueur
try {	
	$requete = "INSERT INTO serveurs (uuid, cleID, etat, ip, dateEnregistrement, nom) VALUES (:uuidServ, :cleID, 'creation', :ipServ, NOW(), :nomServ)";
	
	// Recherche du serveur pour savoir s'il existe déjà
	$reponse = $bdd->prepare ( "SELECT id, etat FROM serveurs WHERE uuid = :uuidServ" );
	$reponse->execute ( array (
			"uuidServ" => $_POST ["uuidServ"] 
	) ) or erreurAPI ( "SQL error 13" );
	$donnees = $reponse->fetch ();
	if ($donnees) {
		// Si le serveur existe déjà
		if ($donnees ["etat"] === "creation") {
			$requete = "UPDATE serveurs SET uuid = :uuidServ, cleID = :cleID, ip = :ipServ, dateEnregistrement = NOW(), nom = :nomServ WHERE uuid = :uuidServ";
		} else {
			ajouterReponse ( "servDejaExistant", "true" );
			ajouterReponse ( "idServ", $donnees ["id"] );
			repondre ();
		}
		$reponse->closeCursor ();
	}
	
	// Création et récupération de la clé d'accès
	$cle = nouvelleCle ( $_POST ["ipJoueur"], $_POST ["uuidJoueur"] );
	
	// Enregistrement du serveur dans la table
	$req = $bdd->prepare ( $requete );
	$req->execute ( array (
			"uuidServ" => $_POST ["uuidServ"],
			"cleID" => $cle->id,
			"ipServ" => $_POST ["ipServ"],
			"nomServ" => $_POST ["nomServ"] 
	) ) or erreurAPI ( "SQL error 37" );
	$req->closeCursor ();
	
	ajouterReponse ( "cle", $cle->cle );
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>