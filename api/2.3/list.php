<?php
require_once 'haut-api.php';

// Téléchargement de la liste des maps disponibles, des notes, des votes, liste des fantômes à envoyer au site, et téléchargement des fantômes disponibles (sans données de checkpoints et position)
// Données POST envoyées : uuidsMapsLocales, joueursConnus, fantomesLocaux, fantomesSupprimes, envoiFantomesAutorise, telechargementFantomesAutorise
try {
	
	$idServ = getIdServValide ( $_POST ["uuidServ"] );
	
	// Enregistrement des maps, joueurs et fantômes connus sur ce serveur
	if ($idServ) {
		$req = $bdd->prepare ( "UPDATE serveurs SET maps = :maps, joueursConnus = :joueurs, fantomes = :fantomes WHERE id = :id" );
		$req->execute ( array (
				'id' => $idServ,
				'maps' => $_POST ["uuidsMapsLocales"],
				'joueurs' => $_POST ["joueursConnus"],
				'fantomes' => $_POST ["fantomesLocaux"] 
		) ) or die ( "SQL error 17" );
		$req->closeCursor ();
	}
	
	// Récupération de la liste des maps
	$mapsPartagees = array ();
	$mapsLocalesServ = explode ( ";", $_POST ["uuidsMapsLocales"] );
	$reponse = $bdd->query ( "SELECT id, uuid, nom, createur, difficulte, etat FROM maps WHERE etat = 'disponible' OR etat = 'supprimee'" ) or erreurAPI ( "SQL error 24" );
	while ( $donnees = $reponse->fetch () ) {
		$mapsPartagees [] = $donnees ["uuid"];
		if (! in_array ( $donnees ["uuid"], $mapsLocalesServ ) && $donnees ["etat"] == "disponible") { // Si la map n'existe pas déjà sur le serveur
			$mapsTelechargeables [] = array (
					"id" => $donnees ["id"],
					"nom" => $donnees ["nom"],
					"createur" => getNomMC ( separerUuidNom ( $donnees ["createur"] ) ["uuid"] ),
					"difficulte" => $donnees ["difficulte"] 
			);
		}
	}
	$reponse->closeCursor ();
	if ($mapsTelechargeables) {
		ajouterReponse ( "maps", $mapsTelechargeables );
	}
	
	// Récupération de la liste des gens qui ont voté pour les maps du serveur
	foreach ( explode ( ";", $_POST ["joueursConnus"] ) as $j ) {
		$expl = explode ( ":", $j );
		$joueursConnusServ [] = $expl [0];
		$nomsJoueursConnus [$expl [0]] = $expl [1];
	}
	$reponse = $bdd->query ( "
			SELECT u.minecraftUUID uuidJoueur, m.uuid uuidMap, m.difficulte difficulte
			FROM votes v
			INNER JOIN utilisateurs u ON v.idUtilisateur = u.id
			INNER JOIN maps m ON v.idMap = m.id
			WHERE v.etat = 'valide'
			" ) or erreurAPI ( "SQL error 53" );
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
	
	// Recherche des changements de noms des joueurs connus
	$reponse = $bdd->query ( "
			SELECT uuid, nom FROM joueursMC WHERE nom <> ''" ) or erreurAPI ( "SQL error 88" );
	while ( $d = $reponse->fetch () ) {
		if (array_key_exists ( $d ["uuid"], $nomsJoueursConnus ) && $d ["nom"] !== $nomsJoueursConnus [$d ["uuid"]]) { // Si le nom de la base est différent de celui envoyé par le serveur
			$nomsChanges [] = array (
					"uuid" => $d ["uuid"],
					"nom" => $d ["nom"] 
			);
		}
	}
	$reponse->closeCursor ();
	if ($nomsChanges) {
		ajouterReponse ( "nomsChanges", $nomsChanges );
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
	$reponse = $bdd->query ( "SELECT uuidMap, uuidJoueur, etat, date, ticks, millisecondes, idServ FROM fantomes ORDER BY id ASC" ) or erreurAPI ( "SQL error 114" );
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
			
			if ($idServ && $donnees ["etat"] == "invalide" && $donnees ["idServ"] && $donnees ["idServ"] != $idServ && ! in_array ( $nomFantome, $fantomesASupprimer )) { // S'il faut supprimer le fantôme
				$fantomesASupprimer [] = $nomFantome;
			} else if ($donnees ["etat"] != "invalide") {
				$fantomesValides [] = $nomFantome;
			}
		} else if (in_array ( $donnees ["uuidMap"], $mapsLocalesServ ))
			$telecharger = true;
		$etat = ($serveurTest && validationFantomesActivee ()) ? 'attente' : 'valide';
		if ($telecharger && $_POST ["telechargementFantomesAutorise"] == "true" && $donnees ["etat"] == $etat && ! in_array ( $nomFantome, $fantomesExclus )) { // Si le téléchargement est autorisé et que le fantôme n'est pas sur le serveur et que le serveur a la map
		                                                                                                                                                        
			// Envoi des données du fantôme, sans les checkpoints et les positions qui seront téléchargés plus tard quand quelqu'un veut voir le fantôme
			$fantomesATelecharger [] = array (
					"uuidMap" => $donnees ["uuidMap"],
					"uuidJoueur" => $donnees ["uuidJoueur"],
					"nomJoueur" => getNomMC ( $donnees ["uuidJoueur"] ),
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
	if ($fantomesASupprimer) {
		// Suppression des fantômes valides du tableau
		foreach ( $fantomesASupprimer as $f ) {
			if (in_array ( $f, $fantomesValides )) {
				$fantomesASupprimer = array_diff ( $fantomesASupprimer, array (
						$f 
				) );
			}
		}
		if ($fantomesASupprimer) {
			ajouterReponse ( "fantomesASupprimer", array_values ( $fantomesASupprimer ) );
		}
	}
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>