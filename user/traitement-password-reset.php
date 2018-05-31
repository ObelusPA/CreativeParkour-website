<?php
if (! $_GET ["t"]) {
	header ( "Location: logout.php" );
	die ();
} else {
	// Vérification de la validité du jeton et récupération des infos
	$reponse = $bdd->prepare ( "SELECT id, nom, passResetToken FROM utilisateurs WHERE minecraftUUID = :uuid AND NOW() < passResetExp ORDER BY id DESC LIMIT 1" );
	$reponse->execute ( array (
			'uuid' => $_GET ["u"] 
	) ) or die ( "SQL error 10" );
	$infos = $reponse->fetch ();
	$reponse->closeCursor ();
	
	// Si pas de résultats ou un mauvais jeton, ça dégage
	if (! $infos ["id"] || ! password_verify ( $_GET ["t"], $infos ["passResetToken"] )) {
		$_SESSION ["erreurs"] [] = "Your request expired or is not valid, please try again.";
		header ( "Location: ../" );
		die ( "Redirecting..." );
	} else {
		if (isset ( $_POST ["reset"] ) && password_verify ( $_GET ["t"], $infos ["passResetToken"] )) {
			// Création des différentes erreurs en fonction de ce que le mec a envoyé
			// ReCaptcha
			if (! verifReCaptcha ()) {
				$_SESSION ["erreurs"] [] = "You did not complete the CAPTCHA verification properly. Please try again.";
			} else {
				// Mot de passe
				if ($_POST ["mdp"]) {
					if (strlen ( $_POST ["mdp"] ) < 6)
						$_SESSION ["erreurs"] [] = "Please enter a password that is at least 6 characters long.";
					else if ($_POST ["mdp"] !== $_POST ["mdpC"])
						$_SESSION ["erreurs"] [] = "Passwords did not match. Please enter the same password in both fields.";
				} else {
					$_SESSION ["erreurs"] [] = "You must choose a password.";
				}
				
				// S'il n'y a aucune erreur, on change le mot de passe et on supprime le jeton
				if (! $_SESSION ["erreurs"]) {
					$req = $bdd->prepare ( "UPDATE utilisateurs SET mdp = :mdp, passResetToken = '', passResetExp = 0 WHERE id = :id" );
					$req->execute ( array (
							'mdp' => password_hash ( $_POST ["mdp"], PASSWORD_DEFAULT ),
							'id' => $infos ["id"] 
					) ) or die ( "SQL error 42" );
					$u->id = $bdd->lastInsertId ();
					$req->closeCursor ();
					
					$_SESSION ["msgOK"] [] = "Your password has been resetted, please try to log in.";
					
					// Terminé !
					retournerOuHeader ( "login.php" );
				}
			}
		}
	}
}
?>