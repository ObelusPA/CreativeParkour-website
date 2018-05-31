<?php
require_once 'includes/haut-global.php';

// Récupération des infos sur la map
$reponse = $bdd->prepare ( "
			SELECT m.id idMap, m.uuid uuidMap, m.nom nomMap, m.description description, DATE_FORMAT(m.dateAjout, '%W, %M %e, %Y') dateAjout, m.idCreateur idCreateur, m.createur createur, m.contributeurs contributeurs, m.image image, m.difficulte difficulte, m.qualite qualite, m.versionMin versionMin, m.verConversion verConversion, s.id idServ, s.etat etatServ, s.nom nomServ
			FROM maps m
			INNER JOIN serveurs s ON m.idServOrigine = s.id
			WHERE m.id = :id AND m.etat = 'disponible'
			ORDER BY m.id DESC LIMIT 1
			" );
$reponse->execute ( array (
		'id' => $_GET ["id"] 
) ) or die ( "SQL error 14" );
$infos = $reponse->fetch ();
$reponse->closeCursor ();

if (! $infos) {
	header ( "Location: maplist.php" );
	die ( "Redirecting..." );
}

$titre = htmlspecialchars ( $infos ["nomMap"] );
$suffixeTitre = "maps";
$metaDescription = "\"" . htmlspecialchars ( $infos ["nomMap"] ) . "\" is a Minecraft parkour map created by " . htmlspecialchars ( getNomMC ( separerUuidNom ( $infos ["createur"] ) ["uuid"] ) ) . " with the CreativeParkour Bukkit plugin.";
require_once 'includes/haut-html.php';

$contributeurs = "";
if ($infos ["contributeurs"]) {
	$contributeurs .= ", ";
	$listeContribs = explode ( ";", $infos ["contributeurs"] );
	foreach ( $listeContribs as $c ) {
		$contributeurs .= nomTete ( separerUuidNom ( $c ) ["uuid"] ) . ", ";
	}
	$contributeurs = substr ( $contributeurs, 0, - 2 );
}
?>
<section class="descriptionMap">
	<h1>Parkour map: <?php echo $titre; ?></h1>
	<table>
		<tr>
			<td><strong><?php echo $infos ["contributeurs"] ? "Creators" : "Creator"; ?></strong>: <?php echo nomTete ( separerUuidNom ( $infos ["createur"] ) ["uuid"])  . $contributeurs; ?><br />
				<strong>Quality</strong>: <?php echo etoiles($infos["qualite"], true); ?><br />
				<strong>Difficulty</strong>: <?php echo texteDifficulte($infos["difficulte"], true); ?><br />
				<strong>Minimum Minecraft version</strong>: <?php echo texteVersion($infos["verConversion"]); ?><br />
				<?php
				if ($infos ["versionMin"] > $infos ["verConversion"]) {
					echo '<span style="font-size: 0.8em;">' . texteConversion ( $infos ["versionMin"], $infos ["verConversion"] ) . '</span><br />';
				}
				?>
				<strong>Shared on</strong>: <?php echo htmlspecialchars($infos["dateAjout"]); ?><br />
				<?php
				if ($infos ["etatServ"] == "public") {
					echo '<strong>Creation server</strong>: <a href="server.php?id=' . htmlspecialchars ( $infos ["idServ"] ) . '">' . htmlspecialchars ( $infos ["nomServ"] ) . '</a><br />';
				}
				if ($infos ["idCreateur"] == $_SESSION ["utilisateur"]->id) {
					echo '<strong><a href="user/map.php?id=' . htmlspecialchars ( $infos ["idMap"] ) . '">Edit map settings</a></strong><br />';
				}
				?>
				<br />&#x27A5; <em>Type "/cpd <?php echo htmlspecialchars($infos["idMap"]); ?>" on a server running CreativeParkour to play! <a
					href="doc/play.php">Help</a></em></td>
			<?php
			if ($infos ["description"]) {
				$descr = $infos ["idCreateur"] == 1 ? $infos ["description"] : htmlspecialchars ( $infos ["description"] );
				echo '<td class="varianteFond" style="width: 60%"><strong>Description</strong>:<br />' . nl2br ( $descr ) . "</td>";
			}
			?>
		</tr>
	</table>
	<?php
	if ($infos ["image"]) {
		echo '<a href="images/maps/' . htmlspecialchars ( rawurlencode ( $infos ["image"] ) ) . '" class="highslide lienImg" onclick="return hs.expand(this)">
			<img class="imgMap" src="images/maps/' . htmlspecialchars ( rawurlencode ( $infos ["image"] ) ) . '" alt="' . htmlspecialchars ( $infos ["nomMap"] ) . ' screenshot" title="Click to enlarge" /></a>';
	}
	?>
</section>
<section>
<?php

// Classement
$reponse = $bdd->prepare ( "SELECT uuidJoueur, ticks FROM fantomes WHERE etat = 'valide' AND uuidMap = :uuidMap ORDER BY ticks, millisecondes LIMIT 40" );
$reponse->execute ( array (
		"uuidMap" => $infos ["uuidMap"] 
) ) or die ( "SQL error 78" );
while ( $donnees = $reponse->fetch () ) {
	$infosFantomes [] = $donnees;
}
$reponse->closeCursor ();

// Recherche du nombre total de fantômes pour cette map
$reponse = $bdd->prepare ( "SELECT COUNT(*) as nb FROM fantomes WHERE etat = 'valide' AND uuidMap = :uuidMap" );
$reponse->execute ( array (
		"uuidMap" => $infos ["uuidMap"] 
) ) or die ( "SQL error 88" );
$nbTotal = $reponse->fetch () ["nb"];
$reponse->closeCursor ();
$nb = count ( $infosFantomes );
if ($nb > 0) {
	echo '<div class="classement">';
	?>
<div class="record">
		<strong>World record: <?php echo htmlspecialchars($infosFantomes[0]["ticks"] * 0.05); ?> seconds</strong><br />
		<em><?php echo nomTete($infosFantomes[0]["uuidJoueur"]); ?></em>
	</div>
<?php
	if ($nb > 1) {
		echo '<p class="colonnes nowrap">';
		$i = 0;
		foreach ( $infosFantomes as $f ) {
			$i ++;
			if ($i > 1) {
				echo htmlspecialchars ( $i ) . ") " . htmlspecialchars ( $f ["ticks"] * 0.05 ) . ": <em>" . nomTete ( $f ["uuidJoueur"] ) . '</em><br />';
			}
		}
		if ($nbTotal > $nb)
			echo '<em style="display:block; text-align: right;">' . htmlspecialchars ( $nbTotal - $nb ) . ' more...</em>';
		echo "</p>";
	}
	echo "</div>";
}
afficherBarrePartage ();
?>
<p class="margeGauche">
		<a
			href="<?php echo htmlspecialchars(strpos($_SERVER["HTTP_REFERER"], "maplist.php") !== false ? $_SERVER["HTTP_REFERER"] : "maplist.php") ?>">Return
			to map list</a>
	</p>

</section>
<?php include_once 'includes/bas.php'; ?>