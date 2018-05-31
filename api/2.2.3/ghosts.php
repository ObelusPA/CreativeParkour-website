<?php
require_once 'haut-api.php';

// Enregistre les fantômes envoyés par le serveur
// Données POST envoyées : fantome-X (pour chaque fantôme)
try {
	// Recherche du serveur
	$idServ = getIdServValide ( $_POST ["uuidServ"] );
	if (! $idServ) {
		erreurAPI ( "Unknown server" );
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
			
			if (! verifBan ( true, null, $uuidJoueur ) && $uuidMap && $uuidJoueur && $nomJoueur && $date && $ticks && $millisecondes && $data && strlen ( $data ) > 50) { // Si le mec du fantôme n'est pas banni et que les données sont bien
			                                                                                                                                                              
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