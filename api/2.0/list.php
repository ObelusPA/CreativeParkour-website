<?php
require_once 'haut-api.php';

// Téléchargement de la liste des maps disponibles
// Données POST envoyées : uuidsMapsLocales, uuidsJoueursConnus
try {
	
	// Récupération de la liste des maps
	$mapsLocalesServ = explode ( ";", $_POST ["uuidsMapsLocales"] );
	$reponse = $bdd->query ( "SELECT id, uuid, nom, createur, difficulte FROM maps WHERE etat = 'disponible'" ) or die ( "SQL error 10" );
	while ( $donnees = $reponse->fetch () ) {
		if (! in_array ( $donnees ["uuid"], $mapsLocalesServ )) { // Si la map n'existe pas déjà sur le serveur
			$mapsTelechargeables [] = array (
					"id" => $donnees ["id"],
					"nom" => $donnees ["nom"],
					"createur" => separerUuidNom ( $donnees ["createur"] ) ["nom"],
					"difficulte" => $donnees ["difficulte"] 
			);
		}
	}
	$reponse->closeCursor ();
	if ($mapsTelechargeables) {
		ajouterReponse ( "maps", $mapsTelechargeables );
	}
	
	// Récupération de la liste des gens qui ont voté pour les maps du serveur
	$joueursConnusServ = explode ( ";", $_POST ["uuidsJoueursConnus"] );
	$reponse = $bdd->query ( "
			SELECT u.minecraftUUID uuidJoueur, m.uuid uuidMap, m.difficulte difficulte
			FROM votes v
			INNER JOIN utilisateurs u ON v.idUtilisateur = u.id
			INNER JOIN maps m ON v.idMap = m.id
			WHERE v.etat = 'valide'
			" ) or die ( "SQL error 32" );
	while ( $donnees = $reponse->fetch () ) {
		if (in_array ( $donnees ["uuidMap"], $mapsLocalesServ ) && in_array ( $donnees ["uuidJoueur"], $joueursConnusServ )) {
			$listeVotes [$donnees ["uuidMap"]] ["joueur"] = $donnees ["uuidJoueur"];
			$listeVotes [$donnees ["uuidMap"]] ["difficulte"] = $donnees ["difficulte"];
		}
	}
	$reponse->closeCursor ();
	if ($listeVotes) {
		// Regroupement des votes de chaque map
		foreach ( $listeVotes as $uuidMap => $vote ) {
			if ($listeTriee) {
				foreach ( $listeTriee as $votesMap ) {
					if ($votesMap ["uuidMap"] == $uuidMap) {
						$ok = true;
						$votesMap ["uuidMap"] ["difficulte"] = $vote ["difficulte"];
						$votesMap ["uuidMap"] ["uuidsJoueurs"] [] = $vote ["joueur"];
					}
				}
			}
			if (! $ok) { // Si la map n'est pas encore dans la liste triée
				$listeTriee [] = array (
						"uuidMap" => $uuidMap,
						"difficulte" => $vote ["difficulte"],
						"uuidsJoueurs" => array (
								$vote ["joueur"] 
						) 
				);
			}
		}
		ajouterReponse ( "votes", $listeTriee );
	}
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>