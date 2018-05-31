<?php
require_once 'haut-api.php';

// Renvoie les données détaillées d'un fantôme au serveur (avec les checkpoints et les positions, contrairement à list.php)
// Données POST envoyées : fantome-X (pour chaque fantôme à renvoyer)
try {
	// Recherche du serveur
	$idServ = getIdServValide ( $_POST ["uuidServ"] );
	if (! $idServ) {
		erreurAPI ( "Unknown server" );
	}
	
	foreach ( $_POST as $post => $dPost ) {
		if (strpos ( $post, "fantome" ) !== false) { // Si c'est bien un fantôme
			$a = explode ( "_", $dPost );
			$uuidMap = $a [0];
			$uuidJoueur = $a [1];
			
			$reponse = $bdd->prepare ( "SELECT uuidMap, uuidJoueur, date, ticks, millisecondes, fichierFantome FROM fantomes WHERE uuidMap = :uuidMap AND uuidJoueur = :uuidJoueur AND etat = 'valide' ORDER BY date DESC LIMIT 1" );
			$reponse->execute ( array (
					"uuidMap" => $uuidMap,
					"uuidJoueur" => $uuidJoueur 
			) ) or erreurAPI ( "SQL error 30" );
			$donnees = $reponse->fetch ();
			$reponse->closeCursor ();
			if ($donnees) {
				// Lecture du fichier
				$zp = gzopen ( "../../maps data/ghosts/" . $donnees ["fichierFantome"] . ".gz", 'r' );
				if (! is_bool ( $zp )) {
					while ( ! gzeof ( $zp ) ) {
						$buffer .= gzread ( $zp, 4096 );
					}
					gzclose ( $zp );
					$fantomesATelecharger [] = array (
							"uuidMap" => $donnees ["uuidMap"],
							"uuidJoueur" => $donnees ["uuidJoueur"],
							"date" => strtotime ( $donnees ["date"] ) * 1000,
							"ticks" => $donnees ["ticks"],
							"millisecondes" => $donnees ["millisecondes"],
							"donnees" => json_decode ( $buffer ) 
					);
					unset ( $buffer );
				}
			}
		}
	}
	if ($fantomesATelecharger) {
		ajouterReponse ( "fantomes", $fantomesATelecharger );
	}
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>