<?php
require_once '/home/creativeeb/www/includes/haut-global.php';

$valeurs = array (
		"nbJoueursCP" => 0,
		"secondesJouees" => 0,
		"parkoursTentes" => 0,
		"parkoursReussis" => 0,
		"nbSauts" => 0 
);
$nbLignes = 0;
$reponse = $bdd->query ( "SELECT nbJoueursCP, secondesJouees, parkoursTentes, parkoursReussis, nbSauts FROM statistiques WHERE nbJoueursCP > 0 AND date >= DATE_SUB(NOW(), INTERVAL 1 WEEK)" ) or die ( "SQL error 12" );
while ( $donnees = $reponse->fetch () ) {
	$nbLignes++;
	foreach ( $donnees as $element => $valeur ) {
		$valeurs [$element] += $valeur;
	}
}
$reponse->closeCursor ();

echo $nbLignes . " relevés statistiques traités.\n";

foreach ( $valeurs as $element => $valeur ) {
	$req = $bdd->prepare ( "UPDATE statsSemaine SET valeur = :valeur, dateMAJ = NOW() WHERE element = :element" );
	$req->execute ( array (
			'element' => $element,
			'valeur' => $valeur 
	) ) or die ( "SQL error 28" );
	$req->closeCursor ();
	
	echo $element . " = " . $valeur . "\n";
}

echo "\nMise à jour effectuée.";
?>