<?php
require_once 'haut-api.php';

// Enregistre les fantômes et les votes envoyés par le serveur
// Données POST envoyées : notes, fantome-X (pour chaque fantôme)
try {
	// Recherche du serveur
	$idServ = getIdServValide ( $_POST ["uuidServ"] );
	if (! $idServ) {
		erreurAPI ( "Unknown server" );
	}

	// Notes
	foreach ( explode ( ";", $_POST ["notes"] ) as $note ) {
		$expl = explode ( ":", $note );
		$key = $expl [0];
		$val = $expl [1];
		$expl = explode ( "_", $key );
		$idMap = getIdMap ( $expl [0] );
		$uuidJoueur = $expl [1];
		$expl = explode ( ",", $val );
		$difficulte = (int) $expl [0];
		$qualite = (int) $expl [1];

		if (($difficulte > 0 || $qualite > 0) && $difficulte <= 5 && $qualite <= 5) {
			// Vérification que le vote n'y est pas déjà
			$reponse = $bdd->prepare ( "SELECT id FROM votes WHERE uuidJoueur = :uuidJoueur AND idMap = :idMap AND difficulte = :d AND qualite = :q" );
			$reponse->execute ( array (
					"idMap" => $idMap,
					"uuidJoueur" => $uuidJoueur,
					"d" => $difficulte,
					"q" => $qualite
			) ) or die ( "SQL error 33" );
			$d = $reponse->fetch ();
			$reponse->closeCursor ();
				
			// Si le vote n'est pas déjà enregistré
			if (! $d) {
				// Péremption des anciens votes non valides
				$req = $bdd->prepare ( "UPDATE votes SET etat = 'perime' WHERE idMap = :idMap AND uuidJoueur = :uuidJoueur AND etat = 'attente' AND idServ = :idServ" );
				$req->execute ( array (
						"idMap" => $idMap,
						"uuidJoueur" => $uuidJoueur,
						"idServ" => $idServ
				) ) or erreurAPI ( "SQL error 45" );
				$req->closeCursor ();

				// Ajout du vote
				$req = $bdd->prepare ( "INSERT INTO votes SET uuidJoueur = :uuidJoueur, idServ = :idServ, idMap = :idMap, etat = 'attente', date = NOW(), difficulte = :d, qualite = :q" );
				$req->execute ( array (
						"idMap" => $idMap,
						"uuidJoueur" => $uuidJoueur,
						"idServ" => $idServ,
						"d" => $difficulte,
						"q" => $qualite
				) ) or erreurAPI ( "SQL error 56" );
				$req->closeCursor ();
			}
		}
	}

	foreach ( $_POST as $post => $dPost ) {
		if (strpos ( $post, "fantome" ) !== false) { // Si c'est bien un fantôme
				
			$json = json_decode ( $dPost );
			$uuidMap = $json->uuidMap;
			$uuidJoueur = $json->uuidJoueur;
			$nomJoueur = $json->nomJoueur;
			$date = $json->date / 1000; // Ce sont des secondes en PHP
			$ticks = $json->ticks;
			$millisecondes = $json->millisecondes;
			$data = json_encode ( $json->data );
				
			// Si le mec du fantôme n'est pas banni et que les données sont bien
			if (! verifBan ( true, null, $uuidJoueur ) && $uuidMap && $uuidJoueur && $nomJoueur && $date && $ticks && $millisecondes && $data && strlen ( $data ) > 50) {

				// Enregistrement du contenu de la map dans un fichier
				$fichierFantome = filtrerNomFichier ( uniqid ( $nomJoueur . "-" ) );
				$fp = gzopen ( "../../maps data/ghosts/" . $fichierFantome . ".gz", "w" );
				gzputs ( $fp, $data );
				gzclose ( $fp );

				// Mise en "périmé" des anciens fantômes de cette map qui ne sont pas encore validés
				$req = $bdd->prepare ( "UPDATE fantomes SET etat = 'perime' WHERE uuidMap = :uuidMap AND (uuidJoueur = :uuidJoueur OR nomJoueur = :nomJoueur) AND etat = 'attente'" );
				$req->execute ( array (
						"uuidMap" => $uuidMap,
						"uuidJoueur" => $uuidJoueur,
						"nomJoueur" => $nomJoueur
				) ) or erreurAPI ( "SQL error 39" );
				$req->closeCursor ();

				// Enregistrement du fantôme dans la table
				$req = $bdd->prepare ( "INSERT INTO fantomes SET uuidMap = :uuidMap, uuidJoueur = :uuidJoueur, nomJoueur = :nomJoueur, etat = 'attente', date = :date, ticks = :ticks, millisecondes = :millisecondes, fichierFantome = :fichierFantome, ipConnexion = :ip, dateEnvoi = NOW(), idServ = :idServ" );
				$req->execute ( array (
						"uuidMap" => $uuidMap,
						"uuidJoueur" => $uuidJoueur,
						"nomJoueur" => $nomJoueur,
						"date" => date ( "Y-m-d H:i:s", $date ),
						"ticks" => $ticks,
						"millisecondes" => $millisecondes,
						"fichierFantome" => $fichierFantome,
						"ip" => getUserIP (),
						"idServ" => $idServ
				) ) or erreurAPI ( "SQL error 53" );
				$req->closeCursor ();
				enregistrerNomMC ( $uuidJoueur, $nomJoueur );
			}
		}
	}
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>