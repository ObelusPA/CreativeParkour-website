<?php
if ($_POST ["login"] && !$_POST ["signup"]) {
	// Recaptcha
	$reponse = $bdd->prepare ( "SELECT nbTentatives FROM tentativesConnexion WHERE ip = :ip AND dernierEssai > DATE_SUB(NOW(), INTERVAL 30 MINUTE)" );
	$reponse->execute ( array (
			'ip' => getUserIP () 
	) ) or die ( "SQL error 7" );
	$donnees = $reponse->fetch ();
	$nbTentatives = 0;
	if ($donnees) {
		$nbTentatives = $donnees ["nbTentatives"];
		if (! verifReCaptcha ())
			$_SESSION ["erreurs"] [] = "You did not complete the CAPTCHA verification properly. Please try again.";
	}
	$reponse->closeCursor ();
	
	if (! $_POST ["mail"])
		$_SESSION ["erreurs"] [] = "Invalid email or name";
	if (! $_POST ["mdp"])
		$_SESSION ["erreurs"] [] = "Invalid password";
	
	if (! $_SESSION ["erreurs"]) {
		$utilisateur = getUtilisateurLocal ( $_POST ["mail"], $_POST ["mdp"] );
		
		if ($utilisateur) {
			$_SESSION ["utilisateur"] = $utilisateur;
			if ($_POST ["remember"])
				rememberMe ();
			retournerOuHeader ( "../" );
		} else {
			$_SESSION ["erreurs"] [] = "Invalid email, name or password.";
		}
	}
	
	if ($_SESSION ["erreurs"]) {
		if ($nbTentatives > 5) {
			bannir ( time () + 2 * 3600, "Security reasons, please come back later.", getUserIP () ); // 2 heures
			header ( "Location: ../" );
			die ();
		} else {
			// Ajout ou mise à jour de la tentative
			if ($nbTentatives == 0)
				$requete = "INSERT INTO tentativesConnexion SET ip = :ip, nbTentatives = :nbTentatives, utilisateur = :utilisateur, dernierEssai = NOW()";
			else
				$requete = "UPDATE tentativesConnexion SET nbTentatives = :nbTentatives, utilisateur = :utilisateur, dernierEssai = NOW() WHERE ip = :ip";
			$req = $bdd->prepare ( $requete );
			$req->execute ( array (
					'ip' => getUserIP (),
					'nbTentatives' => $nbTentatives + 1,
					'utilisateur' => $_POST ["mail"] 
			) ) or die ( "SQL error 49" );
			$req->closeCursor ();
		}
	}
} elseif ($_POST ["signup"]) {
	if ($_SESSION ["cle"])
		$_SESSION ["nouvel utilisateur"] = new Utilisateur ( null, null, null, $_SESSION ["cle"]->uuidJoueur, null, null, null, null );
	header ( "Location: signup.php" );
} elseif (isset ( $_GET ["provider"] )) {
	$provider_name = $_GET ["provider"];
	
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
		$_SESSION ["erreurs"] [] = "Something went wrong while logging you in, sorry.";
		header ( "Location: login.php" );
		die();
	}
	
	// check if the current user already have authenticated using this provider before
	$utilisateur = getUtilisateurSocial ( $provider_name, $user_profile->identifier );
	
	// if the used didn't authenticate using the selected provider before
	// we create a new entry on database.users for him
	if (! $utilisateur) {
		$facebookID = $provider_name == "facebook" ? $user_profile->identifier : null;
		$twitterID = $provider_name == "twitter" ? $user_profile->identifier : null;
		$googleID = $provider_name == "google" ? $user_profile->identifier : null;
		$discordID = $provider_name == "discord" ? $user_profile->identifier : null;
		if ($_SESSION ["cle"] && ($facebookID || $twitterID || $googleID || $discordID))
			$_SESSION ["nouvel utilisateur"] = new Utilisateur ( null, $user_profile->email, null, $_SESSION ["cle"]->uuidJoueur, $facebookID, $twitterID, $googleID, $discordID );
		header ( "Location: signup.php" );
	} else {
		
		// set the user as connected and redirect him
		$_SESSION ["utilisateur"] = $utilisateur;
		if ($_GET ["remember"] == 1)
			rememberMe ();
		retournerOuHeader ( "../" );
	}
}
?>