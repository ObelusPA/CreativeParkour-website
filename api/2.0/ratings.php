<?php
require_once 'haut-api.php';

// Vote pour la difficulté d'une map
// Données POST envoyées : ipJoueur, uuidJoueur, uuidMap, difficulte
try {
	erreurAPI("Please update CreativeParkour to rate maps");
	
	// Vérification de la difficulté
	if ($_POST ["difficulte"] != 1 && $_POST ["difficulte"] != 2 && $_POST ["difficulte"] != 3 && $_POST ["difficulte"] != 4 && $_POST ["difficulte"] != 5) {
		erreurAPI ( "Invalid difficulty" );
		repondre ();
	} else {
		if (! getIdServValide($_POST ["uuidServ"])) {
			// On n'accepte pas
			erreurAPI ( "unknown server", "true" );
		}
		
		// Recherche de l'ID d'utilisateur du mec
		$reponse = $bdd->prepare ( "SELECT id FROM utilisateurs WHERE minecraftUUID = :uuidJoueur" );
		$reponse->execute ( array (
				"uuidJoueur" => $_POST ["uuidJoueur"] 
		) ) or erreurAPI ( "SQL error 21" );
		$donnees = $reponse->fetch ();
		if ($donnees) {
			$idUtilisateur = $donnees ["id"];
		}
		$reponse->closeCursor ();
		
		// Recherche de l'ID de la map
		$reponse = $bdd->prepare ( "SELECT id FROM maps WHERE uuid = :uuidMap" );
		$reponse->execute ( array (
				"uuidMap" => $_POST ["uuidMap"] 
		) ) or erreurAPI ( "SQL error 32" );
		$donnees = $reponse->fetch ();
		if ($donnees) {
			$idMap = $donnees ["id"];
		}
		$reponse->closeCursor ();
		
		if (! $idMap) {
			erreurAPI ( "Unknown map" );
			repondre ();
		} else {
			$requete = "INSERT INTO votes (cleID, idUtilisateur, ipJoueur, idMap, date, etat, difficulte) VALUES (:cleID, :idUtilisateur, :ipJoueur, :idMap, NOW(), 'attente', :difficulte)";
			if ($idUtilisateur) {
				// Recherche de si le mec a déjà voté pour cette map et de son dernier vote
				$reponse = $bdd->prepare ( "SELECT idMap, etat FROM votes WHERE idUtilisateur = :idUtilisateur AND ((idMap = :idMap AND etat = 'valide') OR date > DATE_SUB(NOW(), INTERVAL 6 HOUR))" );
				$reponse->execute ( array (
						"idMap" => $idMap,
						"idUtilisateur" => $idUtilisateur 
				) ) or erreurAPI ( "SQL error 50" );
				while ( $donnees = $reponse->fetch () ) {
					// Si le mec a déjà voté
					if ($donnees ["idMap"] == $idMap) {
						if ($donnees ["etat"] == "valide") {
							ajouterReponse ( "dejaVote", true );
							repondre ();
						} elseif ($donnees ["etat"] == "attente") {
							$requete = "UPDATE votes SET cleID = :cleID, ipJoueur = :ipJoueur, date = NOW(), etat = 'attente', difficulte = :difficulte WHERE idMap = :idMap AND idUtilisateur = :idUtilisateur";
						}
					} else {
						$autorisation = true; // On peut enregistrer le vote sans lui demander de se connecter
					}
				}
				$reponse->closeCursor ();
			}
			
			if ($autorisation) {
				// Enregistrement direct du vote
				$req = $bdd->prepare ( "INSERT INTO votes SET cleID = 0, idUtilisateur = :idUtilisateur, ipJoueur = :ipJoueur, idMap = :idMap, date = NOW(), etat = 'valide', difficulte = :difficulte" );
				$req->execute ( array (
						"idUtilisateur" => $idUtilisateur,
						"ipJoueur" => $_POST ["ipJoueur"],
						"idMap" => $idMap,
						"difficulte" => $_POST ["difficulte"] 
				) ) or erreurAPI ( "SQL error 82" );
				$req->closeCursor ();
				
				calculerDifficulte ( $idMap );
				
				ajouterReponse ( "enregistre", true );
				repondre ();
			} else {
				
				// Création et récupération de la clé d'accès
				$cle = nouvelleCle ( $_POST ["ipJoueur"], $_POST ["uuidJoueur"] );
				
				// Enregistrement du vote dans la table
				$req = $bdd->prepare ( $requete );
				$req->execute ( array (
						"cleID" => $cle->id,
						"idUtilisateur" => $idUtilisateur ? $idUtilisateur : - 1,
						"ipJoueur" => $_POST ["ipJoueur"],
						"idMap" => $idMap,
						"difficulte" => $_POST ["difficulte"] 
				) ) or erreurAPI ( "SQL error 95" );
				$req->closeCursor ();
				
				ajouterReponse ( "cle", $cle->cle );
			}
		}
	}
} catch ( Exception $e ) {
	erreurAPI ( "unknown error" );
}

repondre ();
?>