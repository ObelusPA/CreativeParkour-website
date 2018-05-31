<?php
require_once '/home/creativeeb/www/includes/haut-global.php';

$reponse = $bdd->prepare ( "SELECT uuid, nom FROM joueursMC WHERE dateMAJ <= DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY dateMAJ ASC LIMIT 25" );
$reponse->execute ( array () ) or die ( "SQL error 5" );
while ( $donnees = $reponse->fetch () ) {
	$joueurs [$donnees ["uuid"]] = $donnees ["nom"];
}
$reponse->closeCursor ();
echo htmlspecialchars ( count ( $joueurs ) ) . " joueur(s) à traiter.\n";

// Recherche de joueurs qui ne sont pas dans la table
$reponse = $bdd->prepare ( "SELECT minecraftUUID, nom FROM utilisateurs" );
$reponse->execute ( array () ) or die ( "SQL error 13" );
while ( $donnees = $reponse->fetch () ) {
	$joueursVrac [$donnees ["uuid"]] = $donnees ["nom"];
}
$reponse->closeCursor ();
$reponse = $bdd->prepare ( "SELECT createur, contributeurs FROM maps" );
$reponse->execute ( array () ) or die ( "SQL error 19" );
while ( $donnees = $reponse->fetch () ) {
	$a = separerUuidNom ( $donnees ["createur"] );
	$joueursVrac [$a ["uuid"]] = $a ["nom"];
	// Contributeurs
	foreach ( explode ( ";", $donnees ["contributeurs"] ) as $c ) {
		$a = separerUuidNom ( $c );
		$joueursVrac [$a ["uuid"]] = $a ["nom"];
	}
}
$reponse->closeCursor ();
$reponse = $bdd->prepare ( "SELECT uuidJoueur, nomJoueur FROM fantomes" );
$reponse->execute ( array () ) or die ( "SQL error 31" );
while ( $donnees = $reponse->fetch () ) {
	$joueursVrac [$donnees ["uuidJoueur"]] = $donnees ["nomJoueur"];
}
$reponse = $bdd->prepare ( "SELECT uuid, nom FROM joueursMC" );
$reponse->execute ( array () ) or die ( "SQL error 36" );
while ( $donnees = $reponse->fetch () ) {
	$tousJoueurs [$donnees ["uuid"]] = $donnees ["nom"];
}
$reponse->closeCursor ();
foreach ( $joueursVrac as $uuid => $nom ) {
	if ($uuid && $nom && ! array_key_exists ( $uuid, $tousJoueurs )) { // Si on ne le connaît pas déjà, on l'ajoute
		echo "Enregistrement de " . htmlspecialchars ( $uuid . " (" . $nom ) . ")<br />\n";
		enregistrerNomMC ( $uuid, $nom );
	}
}

foreach ( $joueurs as $uuid => $ancienNom ) {
	if (preg_match ( "#^[0-9a-z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12}$#", $uuid )) {
		// Téléchargement du nom Minecraft
		$profil = telechargerProfilMC ( $uuid );
		$nom = htmlspecialchars ( $profil ["nom"] );
		if ($nom) {
			echo $uuid . " -> " . $nom . "\n";
			// Téléchargement de l'avatar
			$url = 'http://crafatar.com/avatars/' . $uuid . '?overlay';
			$img = '/home/creativeeb/www/images/joueurs/' . $uuid . ".png";
			file_put_contents ( $img, file_get_contents ( $url ) );
			
			// Version 17 pixels
			$url = 'http://crafatar.com/avatars/' . $uuid . '?overlay&size=17';
			$img = '/home/creativeeb/www/images/joueurs/17/' . $uuid . ".png";
			file_put_contents ( $img, file_get_contents ( $url ) );
			
			if ($nom !== $ancienNom) {
				// Mise à jour du nom dans la base
				echo "Le nom de ce joueur est passé de " . htmlspecialchars ( $ancienNom ) . " à " . $nom . ", mise à jour...\n";
				$req = $bdd->prepare ( "UPDATE joueursMC SET nom = :nom, dateMAJ = NOW(), textures = :textures, signature = :signature WHERE uuid = :uuid" );
				$req->execute ( array (
						"uuid" => $uuid,
						"nom" => $nom,
						"textures" => $profil ["textures"],
						"signature" => $profil ["signature"] 
				) ) or erreurAPI ( "SQL error 75" );
				$req->closeCursor ();
			} else
				$forcerMAJ = true;
		} else
			$forcerMAJ = true;
		if ($forcerMAJ) {
			// Si on a pas trouvé, on met à jour la date et les textures quand même
			echo "Nom non trouvé ou pas changé pour " . $uuid . ", mise à jour de la date quand même...\n";
			$req = $bdd->prepare ( "UPDATE joueursMC SET dateMAJ = NOW(), textures = :textures, signature = :signature WHERE uuid = :uuid" );
			$req->execute ( array (
					"uuid" => $uuid,
					"textures" => $profil ["textures"],
					"signature" => $profil ["signature"] 
			) ) or erreurAPI ( "SQL error 89" );
			$req->closeCursor ();
		}
	} else {
		// Si c'est pas un vrai UUID, casse toi
		echo "UUID " . htmlspecialchars ( $uuid ) . " invalide, on le supprime.\n";
		$req = $bdd->prepare ( "DELETE FROM joueursMC WHERE uuid = :uuid" );
		$req->execute ( array (
				"uuid" => $uuid 
		) ) or erreurAPI ( "SQL error 93" );
		$req->closeCursor ();
	}
}
?>