<?php
require_once 'haut-api.php';

// Téléchargement de la liste des maps disponibles, des notes, des votes, liste des fantômes à envoyer au site, et téléchargement des fantômes disponibles (sans données de checkpoints et position)
// Données POST envoyées : uuidsMapsLocales, uuidsJoueursConnus, fantomesLocaux, fantomesSupprimes, envoiFantomesAutorise, telechargementFantomesAutorise
try {
	
	// Récupération de la liste des maps
	$mapsPartagees = array ();
	$mapsLocalesServ = explode ( ";", $_POST ["uuidsMapsLocales"] );
	$reponse = $bdd->query ( "SELECT id, uuid, nom, createur, difficulte FROM maps WHERE etat = 'disponible'" ) or erreurAPI ( "SQL error 11" );
	while ( $donnees = $reponse->fetch () ) {
		$mapsPartagees [] = $donnees ["uuid"];
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
			" ) or erreurAPI ( "SQL error 36" );
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
	
	// Remplissage de la liste des fantômes que le serveur doit envoyer
	$temp = explode ( ";", $_POST ["fantomesLocaux"] );
	// Création du tableau $fantomesServ, sans les fantômes qui ne sont pas pour des maps partagées
	foreach ( $temp as $element ) {
		$d = explode ( ":", $element );
		if (in_array ( explode ( "_", $d [0] ) [0], $mapsPartagees )) {
			$fantomesServ [$d [0]] = ( int ) ($d [1] / 1000); // Secondes et pas millisecondes en php
			if ($_POST ["envoiFantomesAutorise"] == "true") // Si ce n'est pas autorisé, le tableau sera vide
				$fantomesAEnvoyer [] = $d [0];
		}
	}
	$fantomesExclus = explode ( ";", $_POST ["fantomesSupprimes"] );
	$fantomesATelecharger = array ();
	$reponse = $bdd->query ( "SELECT uuidMap, uuidJoueur, nomJoueur, etat, date, ticks, millisecondes FROM fantomes" ) or erreurAPI ( "SQL error 81" );
	while ( $donnees = $reponse->fetch () ) {
		$nomFantome = $donnees ["uuidMap"] . "_" . $donnees ["uuidJoueur"];
		if (array_key_exists ( $nomFantome, $fantomesServ )) { // Si le fantôme est sur le serveur
			$telecharger = false;
			if (strtotime ( $donnees ["date"] ) >= $fantomesServ [$nomFantome]) { // Si le fantôme du serveur n'est pas plus récent
			                                                                      
				// On l'enlève du tableau
				$fantomesAEnvoyer = array_diff ( $fantomesAEnvoyer, array (
						$nomFantome 
				) );
				if (strtotime ( $donnees ["date"] ) > $fantomesServ [$nomFantome]) // Si le fantôme du serveur est trop vieux, il faut le mettre à jour
					$telecharger = true;
			}
		} else if (in_array ( $donnees ["uuidMap"], $mapsLocalesServ ))
			$telecharger = true;
		$etat = ($serveurTest && validationFantomesActivee()) ? 'attente' : 'valide';
		if ($telecharger && $_POST ["telechargementFantomesAutorise"] == "true" && $donnees ["etat"] == $etat && ! in_array ( $nomFantome, $fantomesExclus )) { // Si le téléchargement est autorisé et que le fantôme n'est pas sur le serveur et que le serveur a la map
		  
			// Envoi des données du fantôme, sans les checkpoints et les positions qui seront téléchargés plus tard quand quelqu'un veut voir le fantôme
			$fantomesATelecharger [] = array (
					"uuidMap" => $donnees ["uuidMap"],
					"uuidJoueur" => $donnees ["uuidJoueur"],
					"nomJoueur" => $donnees ["nomJoueur"],
					"date" => strtotime ( $donnees ["date"] ) * 1000,
					"ticks" => $donnees ["ticks"],
					"millisecondes" => $donnees ["millisecondes"] 
			);
		}
	}
	$reponse->closeCursor ();
	if ($fantomesAEnvoyer) {
		ajouterReponse ( "fantomesAEnvoyer", array_values ( $fantomesAEnvoyer ) ); // array_values pour pas que ce soit la merde dans les clés du tableau
	}
	if ($fantomesATelecharger) {
		ajouterReponse ( "fantomesATelecharger", $fantomesATelecharger );
	}
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>