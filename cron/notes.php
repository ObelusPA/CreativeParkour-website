<?php
require_once '/home/creativeeb/www/includes/haut-global.php';

// Validation des notes pour lesquelles il y a un fantôme venant du même serveur

// Récupération des fantômes
$reponse = $bdd->query ( "SELECT m.id idMap, f.uuidJoueur, f.idServ
		FROM fantomes f
		INNER JOIN maps m ON f.uuidMap = m.uuid
		WHERE (f.etat = 'valide' OR f.etat = 'invalide') AND f.triche = 0
		ORDER BY f.dateEnvoi ASC" ) or erreurAPI ( "SQL error 11" );
while ( $donnees = $reponse->fetch () ) {
	$fantomes [$donnees ["idMap"] . "_" . $donnees ["uuidJoueur"]] = $donnees ["idServ"];
	if (! in_array ( $donnees ["idMap"], $idsMaps ))
		$idsMaps [] = $donnees ["idMap"];
}
$reponse->closeCursor ();

// Traitement des votes en attente
$reponse = $bdd->query ( "SELECT id, uuidJoueur, idServ, idMap FROM votes WHERE etat = 'attente' AND date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)" ) or erreurAPI ( "SQL error 15" );
$votes = $reponse->fetchAll ();
$reponse->closeCursor ();
foreach ( $votes as $v ) {
	// S'il y a un fantôme venant du même serveur, on valide le vote
	$idServ = $fantomes [$v ["idMap"] . "_" . $v ["uuidJoueur"]];
	if (($idServ != 0 && $idServ == $v ["idServ"]) || $v ["uuidJoueur"] == "f3301a2d-3319-4e92-98c4-afa5807aed29") {
		// Péremption de l'éventuel ancien vote
		$req = $bdd->prepare ( "UPDATE votes SET etat = 'perime' WHERE idMap = :idMap AND uuidJoueur = :uuidJoueur AND id <> :idVote AND (etat = 'valide' OR etat = 'attente')" );
		$req->execute ( array (
				"idMap" => $v ["idMap"],
				"uuidJoueur" => $v ["uuidJoueur"],
				"idVote" => $v ["id"] 
		) ) or erreurAPI ( "SQL error 31" );
		$req->closeCursor ();
		
		// Validation du fantôme en cours
		$req = $bdd->prepare ( "UPDATE votes SET etat = 'valide' WHERE id = :idVote" );
		$req->execute ( array (
				"idVote" => $v ["id"] 
		) ) or erreurAPI ( "SQL error 38" );
		$req->closeCursor ();
		
		echo "Vote " . htmlspecialchars ( $v ["id"] ) . " validé.\n";
	} else
		echo "Vote " . htmlspecialchars ( $v ["id"] ) . " non validé.\n";
}

// Mise à jour des notes des maps
foreach ( $idsMaps as $i ) {
	calculerNotes ( $i );
	echo "Notes de la map " . htmlspecialchars ( $i ) . " recalculées.\n";
}
?>