<?php
require_once 'includes/haut-global.php';
$titre = "Players";
$noIndex = true;
require_once 'includes/haut-html.php';
?>
<section class="texte">
	<h1>Players</h1>
	<p class="colonnes">
	<?php
	$reponse = $bdd->prepare ( "SELECT uuid FROM joueursMC ORDER BY nom" );
	$reponse->execute ( array () ) or die ( "SQL error 12" );
	while ( $donnees = $reponse->fetch () ) {
		echo nomTete ( $donnees ["uuid"] ) . "<br />";
		$nb ++;
	}
	?>
	</p>
	<p>
	<?php echo htmlspecialchars($nb);?> players
	</p>

</section>
<?php include_once 'includes/bas.php'; ?>