<?php
if ($_POST ["reset"]) {
	if (! verifReCaptcha ())
		$_SESSION ["erreurs"] [] = "You did not complete the CAPTCHA verification properly. Please try again.";
	if (! $_POST ["mail"] || ! filter_var ( $_POST ["mail"], FILTER_VALIDATE_EMAIL ))
		$_SESSION ["erreurs"] [] = "The email you entered is not valid.";
	
	if (! $_SESSION ["erreurs"]) {
		// Anti spam
		if ($_SESSION ["heure envoi mail"] && time () - $_SESSION ["heure envoi mail"] < 120) {
			$_SESSION ["erreurs"] [] = "Please wait before trying to reset your password again.";
		} else {
			// Recherche de l'utilisateur avec l'adresse mail renseignée
			$reponse = $bdd->prepare ( "SELECT id, nom, minecraftUUID FROM utilisateurs WHERE id = :id ORDER BY id DESC LIMIT 1" );
			$reponse->execute ( array (
					'id' => getUserID($_POST ["mail"], true) 
			) ) or die ( "SQL error 13" );
			$d = $reponse->fetch ();
			$reponse->closeCursor ();
			$id = $d ["id"];
			$mail = getMail($d ["id"]);
			$pseudo = $d ["nom"];
			$uuid = $d ["minecraftUUID"];
			if ($id && $mail && $uuid) {
				// Enregistrement et envoi du mail
				$jeton = genererJeton ( 64 );
				$req = $bdd->prepare ( "UPDATE utilisateurs SET passResetToken = :jeton, passResetExp = DATE_ADD(NOW(), INTERVAL 30 MINUTE) WHERE id = :id" );
				$req->execute ( array (
						'id' => $id,
						'jeton' => password_hash ( $jeton, PASSWORD_DEFAULT ) 
				) ) or die ( "SQL error 30" );
				$lien = "https://creativeparkour.net/user/password-reset.php?u=" . htmlspecialchars ( $uuid ) . "&t=" . htmlspecialchars ( $jeton );
				$message = partieMail ( "haut" );
				$message .= '<h1>CreativeParkour.net password reset</h1>';
				$message .= '<p>Hello ' . htmlspecialchars ( $pseudo ) . ', you requested to reset your CreativeParkour.net password, so please click the following link to do it:<br />';
				$message .= '<a href="' . $lien . '">' . $lien . '</a><br /><br />';
				$message .= 'If you have any question or if you were not supposed to receive this, please send an email to obelus@creativeparkour.net<br />';
				$message .= 'Have a nice day!';
				$message .= partieMail ( "bas" );
				envoyerMail ( $mail, "CreativeParkour.net password reset", $message );
			}
			$_SESSION ["msgOK"] [] = "If the address you entered is correct, you received an email to reset your password.";
			$_SESSION ["heure envoi mail"] = time ();
		}
		header ( "Location : ../" );
		die ();
	}
}