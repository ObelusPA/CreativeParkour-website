<?php
// Récupération des infos
$reponse = $bdd->prepare ( "SELECT mdp, nom, DATE_FORMAT(dateCreation, '%W, %M %d, %Y') dateCreation, facebookID, twitterID, googleID, discordID FROM utilisateurs WHERE id = :id ORDER BY id DESC LIMIT 1" );
$reponse->execute ( array (
		'id' => $_SESSION ["utilisateur"]->id 
) ) or die ( "SQL error 6" );
$infos = $reponse->fetch ();
$reponse->closeCursor ();

if ($infos) {
	$infosMail = getInfosMail ( $_SESSION ["utilisateur"]->id );
	
	// On regarde si le mail du mec a été vérifié
	if ($infosMail ["verif"])
		$mailNonVerifie = true;
} else {
	header ( "Location : ../" );
	die ();
}

// Envoi du mail de vérification si le mec le demande
if ($mailNonVerifie && $_GET ["send-email"] == 1) {
	if ($_SESSION ["heure envoi mail"] && time () - $_SESSION ["heure envoi mail"] < 60) {
		$_SESSION ["erreurs"] [] = "Please wait a minute before sending a verification email again.";
	} else {
		ajouterMailEtVerifier ( $_SESSION ["utilisateur"]->id, $_SESSION ["utilisateur"]->mail, $_SESSION ["utilisateur"]->nom, true );
		$_SESSION ["heure envoi mail"] = time ();
	}
}

// Ajout de login avec réseau social
if (isset ( $_GET ["social"] ) && ! $infos [$_GET ["social"] . "ID"]) {
	$provider_name = $_GET ["social"];
	
	try {
		// inlcude HybridAuth library
		// change the following paths if necessary
		$config = '../hybridauth/config.php';
		require_once ("../hybridauth/Hybrid/Auth.php");
		
		// initialize Hybrid_Auth class with the config file
		$hybridauth = new Hybrid_Auth ( $config );
		
		// try to authenticate with the selected provider
		$adapter = $hybridauth->authenticate ( $provider_name );
		
		// then grab the user profile
		$user_profile = $adapter->getUserProfile ();
	}	
	// something went wrong?
	catch ( Exception $e ) {
		$_SESSION ["erreurs"] [] = "Something went wrong, sorry.";
		header ( "Location: /user" );
		die ();
	}
	
	// S'il n'a pas déjà ajouté ce réseau social
	if (! getUtilisateurSocial ( $provider_name, $user_profile->identifier )) {
		if ($provider_name == "facebook") {
			$id = $user_profile->identifier;
			$champ = "facebookID";
		} else if ($provider_name == "twitter") {
			$id = $user_profile->identifier;
			$champ = "twitterID";
		} else if ($provider_name == "google") {
			$id = $user_profile->identifier;
			$champ = "googleID";
		} else if ($provider_name == "discord") {
			$id = $user_profile->identifier;
			$champ = "discordID";
		}
		if (! $id) {
			$_SESSION ["erreurs"] [] = "Something went wrong, sorry.";
		} else {
			// Mise à jour du compte
			$req = $bdd->prepare ( "UPDATE utilisateurs SET " . $champ . " = :idP WHERE id = :id" );
			$req->execute ( array (
					'idP' => $id,
					'id' => $_SESSION ["utilisateur"]->id 
			) ) or die ( "SQL error 86" );
			
			$_SESSION ["msgOK"] [] = "Successfully added your " . ucfirst ( $provider_name ) . " account, you can now login using it!";
			header ( "Location: /user" );
			die ();
		}
	} else {
		$_SESSION ["erreurs"] [] = "This social network account is already in use by someone.";
	}
}

// Traitement du changement de mail
if (isset ( $_POST ["changerMail"] ) && $_POST ["mail"]) {
	$afficherChangementMail = true;
	if (! $_POST ["mail"] || ! filter_var ( $_POST ["mail"], FILTER_VALIDATE_EMAIL )) {
		$_SESSION ["erreurs"] [] = "Please enter a valid email.";
	} else if (strlen ( $_POST ["mail"] ) > 255) {
		$_SESSION ["erreurs"] [] = "Your email is too long.";
	} else if (getMail ( $_SESSION ["utilisateur"]->id ) == $_POST ["mail"]) {
		$_SESSION ["erreurs"] [] = "The new email you typed is the same as your current one.";
	} else if ($infos ["mdp"] && ! password_verify ( $_POST ["mdp"], $infos ["mdp"] )) {
		$_SESSION ["erreurs"] [] = "Invalid password.";
	}
	
	// Recherche de la dernière fois où le mec à changé son mail
	$reponse = $bdd->prepare ( "SELECT id FROM mails WHERE idUtilisateur = :id AND dateAjout > DATE_SUB(NOW(), INTERVAL 2 MINUTE)" );
	$reponse->execute ( array (
			'id' => $_SESSION ["utilisateur"]->id 
	) ) or die ( "SQL error 110" );
	if ($reponse->fetch ()) {
		$_SESSION ["erreurs"] [] = "Please wait before changing your email again.";
	}
	$reponse->closeCursor ();
	
	if (! $_SESSION ["erreurs"]) {
		// Recherche de si le mail est déjà utilisé
		$reponse = $bdd->prepare ( "SELECT id FROM mails WHERE idUtilisateur <> :id AND lower(adresse) = lower(:mail)" );
		$reponse->execute ( array (
				'id' => $_SESSION ["utilisateur"]->id,
				'mail' => $_POST ["mail"] 
		) ) or die ( "SQL error 122" );
		if ($reponse->fetch ()) {
			$_SESSION ["erreurs"] [] = "This email is already used by someone.";
		}
		$reponse->closeCursor ();
	}
	
	// Si pas d'erreur, on change le mail
	if (! $_SESSION ["erreurs"]) {
		// On supprime l'adresse au cas où elle y soit déjà pour forcer sa revalidation
		$req = $bdd->prepare ( "DELETE FROM mails WHERE lower(adresse) = lower(:mail)" );
		$req->execute ( array (
				'mail' => $_POST ["mail"] 
		) ) or die ( "SQL error 135" );
		$req->closeCursor ();
		
		$_SESSION ["msgOK"] [] = "Your email will change as soon as you validate it.";
		ajouterMailEtVerifier ( $_SESSION ["utilisateur"]->id, $_POST ["mail"], $_SESSION ["utilisateur"]->nom, false );
		unset ( $afficherChangementMail );
		header ( "Location: /user" );
		die ();
	}
}

// Traitement du changement de mot de passe
if (isset ( $_POST ["changerPass"] ) && $_POST ["mdp"]) {
	$afficherChangementPass = true;
	if ($infos ["mdp"] && ! password_verify ( $_POST ["mdpA"], $infos ["mdp"] ))
		$_SESSION ["erreurs"] [] = "You did not type your correct current password.";
	else if (strlen ( $_POST ["mdp"] ) < 6)
		$_SESSION ["erreurs"] [] = "Please enter a password that is at least 6 characters long.";
	else if ($_POST ["mdp"] !== $_POST ["mdpC"])
		$_SESSION ["erreurs"] [] = "Passwords did not match. Please enter the same password in both fields.";
	
	// S'il n'y a aucune erreur, on change le mot de passe
	if (! $_SESSION ["erreurs"]) {
		$req = $bdd->prepare ( "UPDATE utilisateurs SET mdp = :mdp WHERE id = :id" );
		$req->execute ( array (
				'mdp' => password_hash ( $_POST ["mdp"], PASSWORD_DEFAULT ),
				'id' => $_SESSION ["utilisateur"]->id 
		) ) or die ( "SQL error 162" );
		$u->id = $bdd->lastInsertId ();
		$req->closeCursor ();
		
		$_SESSION ["msgOK"] [] = "Your password has been successfully changed!";
		unset ( $afficherChangementPass );
		header ( "Location: /user" );
		die ();
	}
}
?>