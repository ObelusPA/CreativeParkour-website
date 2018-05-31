<?php
if ($_SESSION ["cle"]) // Si le mec vient avec une clé, on la supprime en on redirige
{
	// Recherche d'un signalement correspondant à cette clé
	$reponse = $bdd->prepare ( "SELECT id FROM signalementsFantomes WHERE cleID = :cleID ORDER BY id DESC LIMIT 1" );
	$reponse->execute ( array (
			'cleID' => $_SESSION ["cle"]->id 
	) ) or die ( "SQL error 8" );
	$donnees = $reponse->fetch ();
	$reponse->closeCursor ();
	if (! $donnees) {
		$_SESSION ["erreurs"] [] = "Something wrong happened with your request, please try again or report the problem.";
		header ( "Location: ../" );
		die ();
	} else {
		$reportID = $donnees ["id"];
		// Ajout de l'ID de l'utilisateur
		$req = $bdd->prepare ( "UPDATE signalementsFantomes SET idUtilisateur = :idUtilisateur, cleID = 0 WHERE id = :id" );
		$req->execute ( array (
				'idUtilisateur' => $_SESSION ["utilisateur"]->id,
				'id' => $reportID 
		) ) or die ( "SQL error 22" );
		$req->closeCursor ();
	}
	// On renvoie le mec au formulaire de signalement
	unset ( $_SESSION ["cle"] );
	header ( "Location: report-ghost.php?id=" . htmlspecialchars ( $reportID ) );
	die ( "Redirecting..." );
}

// Récupération des données en fonction de l'ID en GET
if ($_GET ["id"]) {
	$reponse = $bdd->prepare ( "
			SELECT sf.id reportID, sf.etat etat, m.nom nomMap, m.uuid uuidMap
			FROM signalementsFantomes sf
			INNER JOIN maps m ON sf.idMap = m.id
			WHERE sf.id = :id AND sf.idUtilisateur = :idUtilisateur
			ORDER BY sf.id DESC LIMIT 1
			" );
	$reponse->execute ( array (
			'id' => $_GET ["id"],
			'idUtilisateur' => $_SESSION ["utilisateur"]->id 
	) ) or die ( "SQL error 43" );
	$infos = $reponse->fetch ();
	$reponse->closeCursor ();
	// Récupération de la liste des fantômes
	$reponse = $bdd->prepare ( "SELECT id, uuidJoueur, ticks FROM fantomes WHERE etat = 'valide' AND uuidMap = :uuidMap ORDER BY ticks" );
	$reponse->execute ( array (
			"uuidMap" => $infos ["uuidMap"] 
	) ) or die ( "SQL error 50" );
	while ( $donnees = $reponse->fetch () ) {
		$fantomes [] = $donnees;
	}
	$reponse->closeCursor ();
	
	if ($infos) {
		if ($infos ["etat"] !== "attente") {
			$_SESSION ["erreurs"] [] = $infos ["etat"] == "validation" ? "Your report has already been sent." : "E49: Something wrong happened with your request, please try again.";
			header ( "Location: ../" );
			die ();
		} else {
			
			// Enregistrement des données du formulaire si elles sont valides
			if ($_POST ["send"]) {
				if (! verifReCaptcha ()) {
					$_SESSION ["erreurs"] [] = "You did not complete the CAPTCHA verification properly. Please try again.";
				} else if (! $_POST ["fantome"]) {
					$_SESSION ["erreurs"] [] = "You must select a ghost to report.";
				} else if (strlen ( $_POST ["raison"] ) > 255) {
					$_SESSION ["erreurs"] [] = "Too much additional information!";
				} else {					
					if (! $_SESSION ["erreurs"]) {
						// Enregistrement des données
						$req = $bdd->prepare ( "UPDATE signalementsFantomes SET etat = 'validation', idFantome = :idFantome, texte = :texte WHERE id = :reportID" );
						$req->execute ( array (
								'idFantome' => $_POST ["fantome"],
								'texte' => $_POST ["raison"],
								'reportID' => $infos ["reportID"] 
						) ) or die ( "SQL error 79" );
						$req->closeCursor ();
						
						$_SESSION ["msgOK"] [] = "Your report has been sent, thank you!";
						header ( "Location: ../" );
						die ( "Redirecting..." );
					}
				}
			}
		}
	}
}
?>