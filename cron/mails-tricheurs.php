<?php
require_once '/home/creativeeb/www/includes/haut-global.php';

$reponse = $bdd->query ( "SELECT id, uuidJoueur, idServ FROM fantomes WHERE notifMail = FALSE AND etat = 'invalide' AND triche = TRUE AND cache = FALSE" ) or die ( "SQL error 4" );
while ( $f = $reponse->fetch () ) {
	$fantomes [$f ["idServ"]] [] = $f ["uuidJoueur"];
	$idsFantomes [] = $f ["id"];
}
$reponse->closeCursor ();

foreach ( $fantomes as $idServ => $list ) {
	$reponse = $bdd->prepare ( "SELECT uuid, nom, idsProprietaires, tricheursIgnores, operateurs FROM serveurs WHERE id = :id AND operateurs <> ''" );
	$reponse->execute ( array (
			'id' => $idServ 
	) ) or die ( "SQL error 15" );
	$infosServ = $reponse->fetch ();
	$proprietaires = explode ( ";", $infosServ ["idsProprietaires"] );
	$tricheursIgnores = array_filter ( array_merge ( explode ( ";", $d ["tricheursIgnores"] ), explode ( ";", $d ["operateurs"] ) ) );
	$reponse->closeCursor ();
	
	$tricheursServ = array_diff ( array_unique ( $fantomes [$idServ] ), $tricheursIgnores );
	
	if ($tricheursServ) {
		// Recherche de leurs noms
		foreach ( $tricheursServ as $uuid ) {
			$nomsTricheurs [] = getNomMC ( $uuid );
		}
		
		// Envoi à chaque propriétaire du serveur qui l'a activé
		$ids = str_repeat ( '?,', count ( $proprietaires ) - 1 ) . '?';
		$reponse = $bdd->prepare ( "SELECT id, minecraftUUID FROM utilisateurs WHERE id IN (" . $ids . ") AND notifsTricheursMail = TRUE" );
		$reponse->execute ( $proprietaires ) or die ( "SQL error 32" );
		while ( $u = $reponse->fetch () ) {
			$mail = getMail ( $u ["id"] );
			if ($mail && mailVerifie ( $u ["id"] )) {
				$lien = 'https://creativeparkour.net/user/uuid-to-id.php?servUUID=' . htmlspecialchars ( $infosServ ["uuid"] ) . '&page=%2Fuser%2Fcheaters.php%3Fserv%3D';
				$message = partieMail ( "haut" );
				$message .= '<h1>Parkour cheaters have been detected on ' . htmlspecialchars ( $infosServ ["nom"] ) . '</h1>';
				$message .= '<p>Hi ' . htmlspecialchars ( getNomMC ( $u ["minecraftUUID"] ) ) . ', CreativeParkour has detected ' . htmlspecialchars ( count ( $tricheursServ ) ) . ' cheater(s) on your Minecraft server (' . htmlspecialchars ( $infosServ ["nom"] ) . '): ';
				$message .= implode ( ", ", $nomsTricheurs ) . ".<br />";
				$message .= '<strong><a href="' . $lien . '">Click here get detailed information about these players</a> (parkour maps, course analysis, useful commands to review ghosts...).</strong><br /><br />';
				$message .= 'This feature is only here to inform you, you are free to do whatever you want with these players. You can manage and disable these alerts <a href="' . $lien . '">here</a>.<br /><br />';
				$message .= 'Thanks for using the CreativeParkour plugin! You can reply to this email if you have any question or suggestion. 🙂';
				$message .= partieMail ( "bas" );
				
				envoyerMail ( $mail, "Cheaters detected on your server", $message );
				echo htmlspecialchars ( "Message envoyé à " . $mail . " (ID utilisateur : " . $u ["id"] . ") pour le serveur " . $infosServ ["nom"] . " (ID serveur : " . $idServ . ").\n" );
			}
		}
		$reponse->closeCursor ();
		
		unset ( $nomsTricheurs );
	}
}

// Marquage des fantômes comme notifiés
if ($idsFantomes) {
	$ids = str_repeat ( '?,', count ( $idsFantomes ) - 1 ) . '?';
	$req = $bdd->prepare ( "UPDATE fantomes SET notifMail = TRUE WHERE id IN (" . $ids . ")" );
	$req->execute ( $idsFantomes ) or die ( "SQL error 60" );
	$req->closeCursor ();
}
?>