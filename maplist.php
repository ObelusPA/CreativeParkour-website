<?php
require_once 'includes/haut-global.php';
$titre = "Parkour maps";
$metaDescription = "List of parkour maps created and shared with the CreativeParkour Bukkit plugin.";
$canonical = "https://creativeparkour.net/maplist.php";
require_once 'includes/haut-html.php';
define ( "MAPS_PAR_PAGE", 10 );
?>
<section class="listeMaps">
	<h1><?php echo htmlspecialchars($titre); ?></h1>
	<p class="margeGauche">
		Here is the list of all the parkour maps created by the
		CreativeParkour community. You can play them by typing the indicated
		command on a server that has the CreativeParkour Bukkit plugin. <a
			href="doc/play.php">Click here</a> for details.
	</p>
	<?php
	$diffMin = - 1;
	$diffMax = 5;
	if ($_GET ["diffMin"])
		$diffMin = ( float ) $_GET ["diffMin"];
	if ($_GET ["diffMax"])
		$diffMax = ( float ) $_GET ["diffMax"];
	if ($diffMax == 5)
		$diffMax = 999; // Pour que les maps à 5 s'affichent
	$verMax = 999; // On veut toutes les versions au départ
	if ($_GET ["verMax"])
		$verMax = ( int ) $_GET ["verMax"];
	// Recherche du nombre de maps
	$reponse = $bdd->prepare ( "SELECT COUNT(*) nombre FROM maps WHERE etat = 'disponible' AND difficulte >= :diffMin AND difficulte < :diffMax AND verConversion <= :verMax" );
	$reponse->bindParam ( ':diffMin', $diffMin, PDO::PARAM_INT );
	$reponse->bindParam ( ':diffMax', $diffMax, PDO::PARAM_INT );
	$reponse->bindParam ( ':verMax', $verMax, PDO::PARAM_INT );
	$reponse->execute () or die ( "SQL error 34" );
	$nbMaps = $reponse->fetch () ["nombre"];
	$reponse->closeCursor ();
	
	$tri = "m.points";
	$sens = "DESC";
	if ($_GET ["s"]) {
		$sens = "ASC";
		if ($_GET ["s"] == "date")
			$tri = "m.dateAjout";
		else if ($_GET ["s"] == "difficulty")
			$tri = "m.difficulte";
		else if ($_GET ["s"] == "quality")
			$tri = "m.qualite";
		else if ($_GET ["s"] == "name")
			$tri = "m.nom";
	}
	if ($_GET ["o"] == "desc")
		$sens = "DESC";
	elseif ($_GET ["o"])
		$sens = "ASC";
	$borneInf = 0;
	$page = 1;
	$nbPages = ceil ( htmlspecialchars ( $nbMaps / MAPS_PAR_PAGE ) );
	if ($_GET ["p"]) {
		$page = ( int ) htmlspecialchars ( $_GET ["p"] );
	}
	if ($page > $nbPages || $page < 1)
		$page = 1;
	else
		$borneInf = ($page - 1) * MAPS_PAR_PAGE;
	function navigation() {
		global $page, $nbPages, $nbMaps;
		$pagePreced = $page - 1;
		$pageSuiv = $page + 1;
		$url = "maplist.php?" . "o=" . htmlspecialchars ( $_GET ["o"] ) . "&s=" . htmlspecialchars ( $_GET ["s"] );
		$urlDiff = "diffMin=" . htmlspecialchars ( $_GET ["diffMin"] ) . "&diffMax=" . htmlspecialchars ( $_GET ["diffMax"] );
		$urlVer = "verMax=" . htmlspecialchars ( $_GET ["verMax"] );
		?>
	
	<div class="navigation">
	Page <?php
		
		echo htmlspecialchars ( $page . "/" . $nbPages . " (" . $nbMaps . " maps)" ) . " &mdash; ";
		if ($pagePreced >= 1) {
			echo '<a rel="nofollow" href="' . $url . "&" . $urlDiff . "&" . $urlVer . '&p=' . $pagePreced . '">Previous</a> &middot; ';
		}
		if ($page - 4 > 1) {
			echo '<a rel="nofollow" href="' . $url . "&" . $urlDiff . "&" . $urlVer . '&p=1">1</a>&middot;';
		}
		if ($page - 5 > 1) {
			echo '&hellip;&middot;';
		}
		if ($page - 4 >= 1) {
			echo '<a rel="nofollow" href="' . $url . "&" . $urlDiff . "&" . $urlVer . '&p=' . ($page - 4) . '">' . ($page - 4) . '</a>&middot;';
		}
		if ($page - 3 >= 1) {
			echo '<a rel="nofollow" href="' . $url . "&" . $urlDiff . "&" . $urlVer . '&p=' . ($page - 3) . '">' . ($page - 3) . '</a>&middot;';
		}
		if ($page - 2 >= 1) {
			echo '<a rel="nofollow" href="' . $url . "&" . $urlDiff . "&" . $urlVer . '&p=' . ($page - 2) . '">' . ($page - 2) . '</a>&middot;';
		}
		if ($page - 1 >= 1) {
			echo '<a rel="nofollow" href="' . $url . "&" . $urlDiff . "&" . $urlVer . '&p=' . ($page - 1) . '">' . ($page - 1) . '</a>&middot;';
		}
		echo '<strong>' . $page . '</strong>';
		if ($page + 1 <= $nbPages) {
			echo '&middot;<a rel="nofollow" href="' . $url . "&" . $urlDiff . "&" . $urlVer . '&p=' . ($page + 1) . '">' . ($page + 1) . '</a>';
		}
		if ($page + 2 <= $nbPages) {
			echo '&middot;<a rel="nofollow" href="' . $url . "&" . $urlDiff . "&" . $urlVer . '&p=' . ($page + 2) . '">' . ($page + 2) . '</a>';
		}
		if ($page + 3 <= $nbPages) {
			echo '&middot;<a rel="nofollow" href="' . $url . "&" . $urlDiff . "&" . $urlVer . '&p=' . ($page + 3) . '">' . ($page + 3) . '</a>';
		}
		if ($page + 4 <= $nbPages) {
			echo '&middot;<a rel="nofollow" href="' . $url . "&" . $urlDiff . "&" . $urlVer . '&p=' . ($page + 4) . '">' . ($page + 4) . '</a>';
		}
		if ($page + 5 < $nbPages) {
			echo '&middot;&hellip;';
		}
		if ($page + 4 < $nbPages) {
			echo '&middot;<a rel="nofollow" href="' . $url . "&" . $urlDiff . "&" . $urlVer . '&p=' . $nbPages . '">' . $nbPages . '</a>';
		}
		if ($pageSuiv <= $nbPages) {
			echo ' &middot; <a rel="nofollow" href="' . $url . "&" . $urlDiff . "&" . $urlVer . '&p=' . $pageSuiv . '">Next</a>';
		}
		?>
	<br /> Sort by : <a rel="nofollow"
			href="maplist.php?s=difficulty&<?php echo htmlspecialchars($urlDiff . "&" . $urlVer); ?>">difficulty</a>
		&middot; <a rel="nofollow"
			href="maplist.php?s=quality&o=desc&<?php echo htmlspecialchars($urlDiff . "&" . $urlVer); ?>">quality</a>
		&middot; <a rel="nofollow"
			href="maplist.php?s=name&<?php echo htmlspecialchars($urlDiff . "&" . $urlVer); ?>">name</a>
		&middot; <a rel="nofollow"
			href="maplist.php?s=date&<?php echo htmlspecialchars($urlDiff . "&" . $urlVer); ?>">date</a> 
			<?php
		// Pas de lien pour l'ordre décroissant si tri par défaut (points)
		if ($_GET ["s"]) {
			echo "(";
			if ($_GET ["o"] == "desc")
				echo '<a rel="nofollow" href="maplist.php?o=asc&s=' . htmlspecialchars ( $_GET ["s"] . "&" . $urlDiff . "&" . $urlVer ) . '">ascending order</a>';
			else
				echo '<a rel="nofollow" href="maplist.php?o=desc&s=' . htmlspecialchars ( $_GET ["s"] . "&" . $urlDiff . "&" . $urlVer ) . '">descending order</a>';
			echo ")";
		}
		?>
			<br /> Search by difficulty: <a rel="nofollow"
			href="<?php echo htmlspecialchars($url . "&" . $urlVer); ?>&diffMin=1&diffMax=1.5">very
			easy</a> &middot; <a rel="nofollow"
			href="<?php echo htmlspecialchars($url . "&" . $urlVer); ?>&diffMin=1.5&diffMax=2.5">easy</a>
		&middot; <a rel="nofollow"
			href="<?php echo htmlspecialchars($url . "&" . $urlVer); ?>&diffMin=2.5&diffMax=3.5">medium</a>
		&middot; <a rel="nofollow"
			href="<?php echo htmlspecialchars($url . "&" . $urlVer); ?>&diffMin=3.5&diffMax=4.5">hard</a>
		&middot; <a rel="nofollow"
			href="<?php echo htmlspecialchars($url . "&" . $urlVer); ?>&diffMin=4.5&diffMax=5">extreme</a>
		&middot; <a rel="nofollow"
			href="<?php echo htmlspecialchars($url . "&" . $urlVer); ?>">all</a>
		<br /> Minecraft version compatibility: <a rel="nofollow"
			href="<?php echo htmlspecialchars($url . "&" . $urlDiff); ?>&verMax=8">1.8</a>
		&middot; <a rel="nofollow"
			href="<?php echo htmlspecialchars($url . "&" . $urlDiff); ?>&verMax=9">1.9</a>
		&middot; <a rel="nofollow"
			href="<?php echo htmlspecialchars($url . "&" . $urlDiff); ?>&verMax=10">1.10</a>
		&middot; <a rel="nofollow"
			href="<?php echo htmlspecialchars($url . "&" . $urlDiff); ?>&verMax=11">1.11</a>
		&middot; <a rel="nofollow"
			href="<?php echo htmlspecialchars($url . "&" . $urlDiff); ?>&verMax=12">1.12</a>
	</div>
	<?php
	}
	navigation ();
	if ($nbMaps < 1) {
		?>
	<div class="avertissement">
		0 maps to display with your criteria (<a href="maplist.php">reset</a>).
	</div>
	<?php
	}
	?>
	<table>
	<?php
	$reponse = $bdd->prepare ( "
			SELECT m.id idMap, m.nom nomMap, s.id idServ, s.nom nomServ, s.etat etatServ, m.createur createurMap, m.contributeurs contributeursMap, m.image imageMap, m.difficulte difficulte, m.qualite qualite, m.versionMin versionMin, m.verConversion verConversion
			FROM maps m
			INNER JOIN serveurs s ON m.idServOrigine = s.id
			WHERE m.etat = 'disponible' AND difficulte >= :diffMin AND difficulte < :diffMax AND verConversion <= :verMax
			ORDER BY " . $tri . " " . $sens . ", m.points DESC, m.id DESC LIMIT :borneInf, " . MAPS_PAR_PAGE . "
			" );
	$reponse->bindParam ( ':diffMin', $diffMin, PDO::PARAM_INT );
	$reponse->bindParam ( ':diffMax', $diffMax, PDO::PARAM_INT );
	$reponse->bindParam ( ':verMax', $verMax, PDO::PARAM_INT );
	$reponse->bindParam ( ':borneInf', $borneInf, PDO::PARAM_INT );
	$reponse->execute () or die ( "SQL error 186" );
	while ( $donnees = $reponse->fetch () ) {
		afficherLigneMap ( $donnees );
	}
	$reponse->closeCursor ();
	?>
	<tr class="ligneSupplementaire">
			<td colspan="3"><a href="doc/add-map.php">Add my parkour map</a></td>
		</tr>
	</table>
	<?php navigation(); ?>
</section>
<?php include_once 'includes/bas.php'; ?>