<?php
$nu = $_SESSION ["nouvel utilisateur"];
if (! $nu) { // Si le mec n'a pas été envoyé par le plugin, il dégage
	$_SESSION ["erreurs"] [] = "For security reasons, you have to use the \"/cp register\" command on a Minecraft server that runs CreativeParkour to sign up here. Just go in Minecraft and type \"/cp register\".";
	header ( "Location: ../" );
	die ();
}
$utilisateurSocial = $nu && ($nu->facebookID || $nu->twitterID || $nu->googleID || $nu->discordID);
$mailConnu = $utilisateurSocial && $nu->mail;
if (! isset ( $_SESSION ["nomMC"] )) {
	$_SESSION ["nomMC"] = getNomMC ( $_SESSION ["cle"]->uuidJoueur );
}
// Nom Minecraft
if (! $_SESSION ["nomMC"]) {
	$_SESSION ["erreurs"] [] = "Something went wrong while checking your Minecraft UUID sent by your server (" . htmlspecialchars ( $_SESSION ["cle"]->uuidJoueur ) . "), it is probably not linked to a Minecraft account. Please try again after checking your server configuration, or report this error. If your server is using BungeeCord, please read this: https://www.spigotmc.org/wiki/bungeecord-installation/#post-installation";
}
if (isset ( $_POST ["signup"] )) {
	// Création des différentes erreurs en fonction de ce que le mec a envoyé
	// ReCaptcha
	if (! verifReCaptcha ()) {
		$_SESSION ["erreurs"] [] = "You did not complete the CAPTCHA verification properly. Please try again.";
	} else {
		// Mail
		$reponse = $bdd->prepare ( "SELECT facebookID AS fb, twitterID AS tw, googleID AS gl, discordID AS di FROM utilisateurs WHERE id = :id OR minecraftUUID = :mcUUID ORDER BY id DESC LIMIT 1" );
		$reponse->execute ( array (
				'id' => getUserID ( $mailConnu ? $nu->mail : $_POST ["mail"], false ),
				'mcUUID' => $_SESSION ["cle"]->uuidJoueur 
		) ) or die ( "SQL error 26" );
		$donnees = $reponse->fetch ();
		if ($donnees) {
			// Création de la phrase de l'erreur
			$phrase = "Your Minecraft account or the email you specified are already used.";
			if ($donnees ["fb"] || $donnees ["tw"] || $donnees ["gl"] || $donnees ["di"]) {
				$phrase = $phrase . " They are associated with a ";
				if ($donnees ["fb"])
					$phrase = $phrase . "Facebook";
				elseif ($donnees ["tw"])
					$phrase = $phrase . "Twitter";
				elseif ($donnees ["gl"])
					$phrase = $phrase . "Google";
				elseif ($donnees ["di"])
					$phrase = $phrase . "Discord";
				$phrase = $phrase . " account.";
			} else {
				$phrase = $phrase . " They are not associated with a social network account.";
			}
			$_SESSION ["erreurs"] [] = $phrase;
		} else if (! $mailConnu) {
			if (! $_POST ["mail"] || ! filter_var ( $_POST ["mail"], FILTER_VALIDATE_EMAIL )) {
				$_SESSION ["erreurs"] [] = "Please enter a valid email.";
			} else if (strlen ( $_POST ["mail"] ) > 255) {
				$_SESSION ["erreurs"] [] = "Your email is too long.";
			}
		}
		$reponse->closeCursor ();
		// Mot de passe
		if ($_POST ["mdp"]) {
			if (strlen ( $_POST ["mdp"] ) < 6)
				$_SESSION ["erreurs"] [] = "Please enter a password that is at least 6 characters long.";
			elseif ($_POST ["mdp"] !== $_POST ["mdpC"])
				$_SESSION ["erreurs"] [] = "Passwords did not match. Please enter the same password in both fields.";
		} else if (! $utilisateurSocial) {
			$_SESSION ["erreurs"] [] = "You must choose a password.";
		}
		
		// Acceptation des règles
		/*
		 * if ($_POST ["règles"] !== "Oui") {
		 * $_SESSION ["erreurs"] [] = "You have to accept the rules.";
		 * }
		 */
		
		// S'il n'y a aucune erreur, on crée le compte
		if (! $_SESSION ["erreurs"]) {
			$u = new Utilisateur ( null, $mailConnu ? $nu->mail : $_POST ["mail"], $_SESSION ["nomMC"], $_SESSION ["cle"]->uuidJoueur, $nu->facebookID, $nu->twitterID, $nu->googleID, $nu->discordID );
			
			// Insertion dans la base de données
			$req = $bdd->prepare ( "INSERT INTO utilisateurs SET mdp = :mdp, nom = :nom, dateCreation = NOW(), facebookID = :fbID, twitterID = :twID, googleID = :glID, discordID = :diID, minecraftUUID = :mcUUID" );
			$req->execute ( array (
					'mdp' => $_POST ["mdp"] ? password_hash ( $_POST ["mdp"], PASSWORD_DEFAULT ) : "",
					'nom' => $u->nom,
					'fbID' => nullVide ( $u->facebookID ),
					'twID' => nullVide ( $u->twitterID ),
					'glID' => nullVide ( $u->googleID ),
					'diID' => nullVide ( $u->discordID ),
					'mcUUID' => $u->minecraftUUID 
			) ) or die ( "SQL error 84" );
			$u->id = $bdd->lastInsertId ();
			$req->closeCursor ();
			unset ( $_SESSION ["nouvel utilisateur"] );
			
			// Envoi éventuel du mail de vérification
			ajouterMailEtVerifier ( $u->id, $u->mail, $u->nom, true, ! $mailConnu ); // Si on a reçu le mail via un réseau social, pas besoin de vérifier
			
			$_SESSION ["msgOK"] [] = "Your account has been successfully created, welcome!";
			
			$_SESSION ["utilisateur"] = $u;
			
			// Inscription terminée
			retournerOuHeader ( "../" );
		}
	}
}
?>