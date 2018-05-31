<?php
// Blocage si pas HTTPS
if ($_SERVER ["HTTPS"] != "on") {
	http_response_code ( 403 );
	die ();
}
require_once '../../includes/haut-global.php';
// Blocage si pas connecté avec le bon compte
if ($_SESSION ["utilisateur"]->id != 1 || $_SESSION ["utilisateur"]->mail != "obelus@creativeparkour.net") {
	http_response_code ( 403 );
	die ();
}

// Fonctions spécifiques à l'administration
function afficherNbRouge($nb) {
	if ($nb > 0)
		echo '&nbsp;<strong style="color:red">(' . htmlspecialchars ( $nb ) . ')</strong>';
}

$noIndex = true;
$pasAnalytics = true;
require_once '../../includes/haut-html.php';

// Récupération des infos pour la barre

$reponse = $bdd->query ( "SELECT COUNT(*) as nb FROM fantomes WHERE etat = 'attente'" ) or die ( "SQL error 115" );
$nbFantomesAFaire = $reponse->fetch () ["nb"];
$reponse->closeCursor ();

$reponse = $bdd->query ( "SELECT COUNT(*) as nb FROM erreurs WHERE traitement = 'attente'" ) or die ( "SQL error 119" );
$nbErreurs = $reponse->fetch () ["nb"];
$reponse->closeCursor ();

$reponse = $bdd->query ( "SELECT COUNT(*) as nb FROM signalementsFantomes WHERE etat = 'validation'" ) or die ( "SQL error 123" );
$nbSignalements = $reponse->fetch () ["nb"];
$reponse->closeCursor ();
?>
<div style="background-color: #c4e2ff; text-align: center;">
	🔒 <a href="user/admin!">Administration</a> › <a
		href="user/admin!/fantomes.php">Validation des fantômes</a><?php afficherNbRouge($nbFantomesAFaire); ?>
		 &middot; <a href="user/admin!/erreurs.php">Erreurs</a><?php afficherNbRouge($nbErreurs); ?>
		 &middot; <a href="user/admin!/signalements.php">Signalements de
		fantômes</a><?php afficherNbRouge($nbSignalements); ?>
</div>