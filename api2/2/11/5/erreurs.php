<?php
require_once 'haut-api.php';

// Reçu des envois de statistiques
// Données POST envoyées : versionServeur, erreur, onlineMode

// Recherche de la dernière erreur identique
$reponse = $bdd->prepare ( "SELECT id FROM erreurs WHERE uuidServeur = :uuidServeur AND erreur = :erreur AND versionPlugin = :versionPlugin AND versionServeur = :versionServeur AND date > DATE_SUB(NOW(), INTERVAL 1 HOUR)" );
$reponse->execute ( array (
		"uuidServeur" => $_POST["uuidServ"],
		"erreur" => $_POST ["erreur"],
		"versionPlugin" => $_POST ["versionPlugin"],
		"versionServeur" => $_POST ["versionServeur"] 
) ) or erreurAPI ( "SQL error 14" );
$donnees = $reponse->fetch ();
$reponse->closeCursor ();
if (! $donnees) {
	// Ajout des données dans la table
	$req = $bdd->prepare ( "INSERT INTO erreurs (date, erreur, uuidServeur, versionPlugin, versionServeur, onlineMode, traitement) VALUES (NOW(), :erreur, :uuidServeur, :versionPlugin, :versionServeur, :onlineMode, 'attente')" );
	$req->execute ( array (
			"erreur" => $_POST ["erreur"],
			"uuidServeur" => $_POST["uuidServ"],
			"versionPlugin" => $_POST ["versionPlugin"],
			"versionServeur" => $_POST ["versionServeur"],
			"onlineMode" => $_POST ["onlineMode"] 
	) ) or erreurAPI ( "SQL error 26" );
	$req->closeCursor ();
}

repondre ();
?>