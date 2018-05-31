<?php
require_once 'haut-api.php';

// Reçu des envois de statistiques
// Données POST envoyées : nbJoueurs, nbMaps, nbJoueursCP, secondesJouees, parkoursTentes, parkoursReussis, nbSauts, langue, onlineMode, infosPlugin, versionServeur, commandes, plugins, operateurs
try {
	
	$servID = getServIDAvecUUID ( $_POST ["uuidServ"] );
	
	// Ajout des données dans la table
	$req = $bdd->prepare ( "INSERT INTO statistiques (date, idServ, uuidServ, ipConnexion, nbJoueurs, nbMaps, nbJoueursCP, secondesJouees, parkoursTentes, parkoursReussis, nbSauts, langue, onlineMode, infosPlugin, versionServeur, commandes) VALUES (NOW(), :idServ, :uuidServ, :ipConnexion, :nbJoueurs, :nbMaps, :nbJoueursCP, :secondesJouees, :parkoursTentes, :parkoursReussis, :nbSauts, :langue, :onlineMode, :infosPlugin, :versionServeur, :commandes)" );
	$req->execute ( array (
			"idServ" => $servID,
			"uuidServ" => $_POST ["uuidServ"],
			"ipConnexion" => getUserIP (),
			"nbJoueurs" => $_POST ["nbJoueurs"],
			"nbMaps" => $_POST ["nbMaps"],
			"nbJoueursCP" => $_POST ["nbJoueursCP"],
			"secondesJouees" => $_POST ["secondesJouees"],
			"parkoursTentes" => $_POST ["parkoursTentes"],
			"parkoursReussis" => $_POST ["parkoursReussis"],
			"nbSauts" => $_POST ["nbSauts"],
			"langue" => $_POST ["langue"],
			"onlineMode" => $_POST ["onlineMode"],
			"infosPlugin" => $_POST ["infosPlugin"],
			"versionServeur" => $_POST ["versionServeur"],
			"commandes" => $_POST ["commandes"]
	) ) or erreurAPI ( "SQL error 28" );
	$req->closeCursor ();
	
	// Ajout de la liste des plugins installés et des opérateurs
	if ($servID) {
		$req = $bdd->prepare ( "UPDATE serveurs SET plugins = :plugins, operateurs = :ops WHERE id = :id" );
		$req->execute ( array (
				"id" => $servID,
				"plugins" => $_POST ["plugins"],
				"ops" => $_POST ["operateurs"]
		) ) or erreurAPI ( "SQL error 38" );
		$req->closeCursor ();
	}
	
	foreach ( $_POST as $post => $val ) {
		if (strpos ( $post, "tentatives" ) === 0) {
			$uuidMap = substr ( $post, strpos ( $post, "-" ) + 1 );
			// Récupération du nombre de tentatives existantes dans la map
			$reponse = $bdd->prepare ( "SELECT id, tentatives FROM maps WHERE uuid = :uuid" );
			$reponse->execute ( array (
					'uuid' => $uuidMap
			) ) or die ( "SQL error 79" );
			$d = $reponse->fetch ();
			$reponse->closeCursor ();
			// Si la map existe, on ajoute les nouvelles tentatives
			if ($d) {
				$anciennesTentatives = tentativesToArray ( $d ["tentatives"] );
				foreach ( tentativesToArray ( $val ) as $uuidJ => $nb ) {
					$anciennesTentatives [$uuidJ] += $nb;
				}
				
				// Enregistrement des nouvelles données
				$req = $bdd->prepare ( "UPDATE maps SET tentatives = :tentatives WHERE id = :id" );
				$req->execute ( array (
						"tentatives" => arrayToTentatives ( $anciennesTentatives ),
						"id" => $d ["id"]
				) ) or erreurAPI ( "SQL error 94" );
				$req->closeCursor ();
			}
		}
	}
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>