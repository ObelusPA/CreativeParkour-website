<?php
$titre = "Erreurs";
require_once 'haut-admin.php';

// Traitement du formulaire
if ($_POST ["ok"]) {
	if (count ( $_POST ["sélection"] ) > 0) {
		$strReq = "UPDATE erreurs SET traitement = 'traitee' WHERE";
		$arrayReq = array ();
		$i = 0;
		
		foreach ( $_POST ["sélection"] as $idErr ) {
			// Ajout de l'erreur à la requête finale
			if ($i > 0)
				$strReq .= " OR";
			$strReq .= " id = :id" . $i;
			$arrayReq ["id" . $i] = $idErr;
			
			$i ++;
		}
		
		$req = $bdd->prepare ( $strReq );
		$req->execute ( $arrayReq ) or die ( "SQL error 23" );
		$req->closeCursor ();
		
		header ( "Location: erreurs.php" );
		die ();
	}
}
?>
<section class="texte">
	<h1>Erreurs</h1>
	<p>
		Il y a <?php echo htmlspecialchars($nbErreurs); ?> erreur(s) à traiter.
	</p>
	<form action="user/admin!/erreurs.php" method="POST">
		<p>
	<?php
	$reponse = $bdd->prepare ( "
			SELECT id, DATE_FORMAT(date, '%d/%m/%Y à %H:%i:%s') date, erreur, versionPlugin, versionServeur, onlineMode
			FROM erreurs
			WHERE traitement = 'attente'
			" );
	$reponse->execute ( array () ) or die ( "SQL error 68" );
	while ( $d = $reponse->fetch () ) {
		$donneesE [] = $d;
	}
	$reponse->closeCursor ();
	foreach ( $donneesE as $d ) {
		// Affichage de la ligne
		$id = htmlspecialchars ( $d ["id"] );
		echo '<input type="checkbox" name="sélection[]" value="' . $id . '" id="e' . $id . '"> 
			<label for="e' . $id . '">Le ' . htmlspecialchars ( $d ["date"] . ' (CreativeParkour ' . $d ["versionPlugin"] . ' sur ' . $d ["versionServeur"] . ($d ["onlineMode"] == "false" ? " (offline)" : "") ) . ')</label><br />';
		echo '<span class="ligneInfosListe">' . nl2br ( htmlspecialchars ( $d ["erreur"] ) ) . '</span><br />';
	}
	?><br /> <input type="submit" name="ok"
				value="Marquer la sélection comme traitée" />
		</p>
	</form>
</section>
<?php include_once '../../includes/bas.php'; ?>