<?php
require_once '/home/creativeeb/www/includes/haut-global.php';

$reponse = $bdd->query ( "SELECT id, uuid, nom, dateAjout, qualite, fichierContenu FROM maps WHERE etat = 'disponible'" ) or die ( "SQL error 6" );
while ( $d = $reponse->fetch () ) {
	$maps [$d ["uuid"]] = $d;
}
$reponse->closeCursor ();

$reponse = $bdd->query ( "SELECT uuidMap, uuidJoueur, etat, ticks FROM fantomes WHERE etat = 'valide' OR etat = 'perime'" ) or die ( "SQL error 10" );
while ( $d = $reponse->fetch () ) {
	$temps [] = $d;
	// $tempsJoueurs [$d ["uuidJoueur"]] [] = $d;
	// if (key_exists ( $d ["uuidMap"], $maps ))
	// $tempsMaps [$d ["uuidMap"]] [] = $d;
}
$reponse->closeCursor ();

echo "*****************************************************************\nCalcul des scores des maps...\n*****************************************************************\n";
// Calcul du temps moyen et du pourcentage de temps périmés pour chaque map, et comptage des blocs...
foreach ( $temps as $t ) {
	if (key_exists ( $t ["uuidMap"], $maps )) {
		if ($t ["etat"] == "valide") {
			$maps [$t ["uuidMap"]] ["sommeTemps"] += $t ["ticks"];
			$maps [$t ["uuidMap"]] ["tempsValides"] ++;
		} else
			$maps [$t ["uuidMap"]] ["tempsPerimes"] ++;
	}
}
$partPerimesMax = 0;
foreach ( $maps as $uuidMap => $map ) {
	if ($map ["tempsValides"] <= 0)
		$mapsSansTemps [] = $uuidMap;
	else
		$moyennesMaps [$uuidMap] = $map ["sommeTemps"] / $map ["tempsValides"];
	
	$pp = min ( 1, max ( 0, $map ["tempsPerimes"] / $map ["tempsValides"] ) );
	$maps [$uuidMap] ["partPerimes"] = $pp;
	if ($pp > $partPerimesMax)
		$partPerimesMax = $pp;
	
	// Blocs
	$dMap = new Map ( $uuidMap );
	$diff = count ( $dMap->types );
	$blocs = count ( $dMap->blocs );
	$mapsBlocsDiff [$uuidMap] = $diff;
	$mapsNbBlocs [$uuidMap] = $blocs;
}
// Ajout des maps sans temps avec la valeur moyenne
$moy = array_sum ( $moyennesMaps ) / count ( $moyennesMaps );
foreach ( $mapsSansTemps as $uuid ) {
	$moyennesMaps [$uuid] = $moy;
}
asort ( $moyennesMaps );
asort ( $mapsBlocsDiff );
asort ( $mapsNbBlocs );
$moyenneMin = $moyennesMaps [array_keys ( $moyennesMaps ) [0.15 * count ( $moyennesMaps )]]; // Moyenne de la map à 15 % de l'effectif
echo "Moyenne des temps pour avoir tous les points : " . htmlspecialchars ( $moyenneMin ) . "\n";

// Calcul des points de chaque map
foreach ( $maps as $uuidMap => $map ) {
	echo "\n>>> Map : " . htmlspecialchars ( $map ["nom"] ) . " (" . htmlspecialchars ( $map ["id"] ) . ")\n";
	$points = 0;
	
	// Récentitude
	$anciennete = time () - strtotime ( $map ["dateAjout"] );
	$p = 40 * (max ( 5184000 - $anciennete, 0 ) / 5184000); // 5184000 = 60 jours
	echo "Récentitude : " . htmlspecialchars ( $p ) . "/40\n";
	$points += $p;
	
	// Qualité
	$p = 30 * (($map ["qualite"] > 0 ? $map ["qualite"] : 3) / 5); // Si pas de vote de qualité, on met 3...
	echo "Qualité : " . htmlspecialchars ( $p ) . "/30\n";
	$points += $p;
	
	// Moyenne des temps
	$p = min ( 10, 10 * ($moyennesMaps [$uuidMap] / $moyenneMin) );
	echo "Temps moyen : " . htmlspecialchars ( $p ) . "/10\n";
	$points += $p;
	
	// Pourcentage de temps périmés
	$p = 10 * ($map ["partPerimes"] / $partPerimesMax);
	echo "% temps périmés : " . htmlspecialchars ( $p ) . "/10\n";
	$points += $p;
	
	// Nombre de blocs différents
	$p = 5 * (array_search ( $uuidMap, array_keys ( $mapsBlocsDiff ) ) / (count ( $mapsBlocsDiff ) - 1));
	echo "Blocs différents : " . htmlspecialchars ( $p ) . "/5\n";
	$points += $p;
	
	// Nombre de blocs
	$p = 5 * (array_search ( $uuidMap, array_keys ( $mapsNbBlocs ) ) / (count ( $mapsNbBlocs ) - 1));
	echo "Nombre de blocs : " . htmlspecialchars ( $p ) . "/5\n";
	$points += $p;
	
	echo "-> " . htmlspecialchars ( $points ) . " points\n";
	
	// Mise à jour
	$req = $bdd->prepare ( "UPDATE maps SET points = :points WHERE id = :id" );
	$req->execute ( array (
			"id" => $map ["id"],
			"points" => $points 
	) ) or erreurAPI ( "SQL error 95" );
	$req->closeCursor ();
}



echo "*****************************************************************\nCalcul des scores des joueurs...\n*****************************************************************\n";
?>