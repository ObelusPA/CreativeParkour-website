<?php
require_once 'includes/haut-global.php';

// Ajout des erreurs http s'il y en a
if ($_GET["httperror"]) {
    $err = "HTTP error " . htmlspecialchars($_GET["httperror"]);
    if ($_GET["httperror"] == 404)
        $err .= " (the requested page could not be found)";
    $_SESSION["erreurs"][] = $err;
}

$suffixeTitre = "&bull; Bukkit parkour plugin";
$metaDescription = "CreativeParkour is a Bukkit plugin that allows everyone to play, create and share fun parkour maps on Minecraft servers running Spigot.";
$canonical = "https://creativeparkour.net";
require_once 'includes/haut-html.php';
?>
<section class="texte">
	<h1><?php echo connecte()? "Hello " . htmlspecialchars($_SESSION["utilisateur"]->nom) . "!" : "Welcome!";?></h1>
	<p>
		CreativeParkour is a Bukkit plugin that allows players to create,
		publish, share, download and play fun parkour maps on Minecraft
		servers running Spigot.<br /> This website is here to show parkour
		maps that have been created and shared by players all around the
		world. These maps can be downloaded to servers with the
		CreativeParkour plugin and everyone can play them (<a
			href="doc/play.php">how?</a>).<br /> <br /> <strong>Useful links:</strong><br />
		&nbsp;&bull; <a href="maplist.php">Parkour map list</a><br />
		&nbsp;&bull; <a href="doc/play.php">How to play these maps?</a><br />
		&nbsp;&bull; <a href="doc/map-creation.php">Map creation tutorial</a><br />
		&nbsp;&bull; <a href="doc/commands.php">Plugin commands</a><br />
		&nbsp;&bull; <a target="_blank"
			href="http://dev.bukkit.org/bukkit-plugins/creativeparkour/">CreativeParkour
			download and information on Bukkit.org</a><br /> &nbsp;&bull; <a
			target="_blank"
			href="https://www.spigotmc.org/resources/creativeparkour.17303/">CreativeParkour
			download and information on Spigotmc.org</a>
	</p>
</section>
<section class="listeMaps">
	<h2 class="margeGauche" style="margin-bottom: 5px;">Featured parkour
		maps</h2>
	<table>
		<?php
$reponse = $bdd->query("
					SELECT m.id idMap, m.nom nomMap, s.id idServ, s.nom nomServ, s.etat etatServ, m.createur createurMap, m.contributeurs contributeursMap, m.image imageMap, m.difficulte difficulte, m.qualite qualite, m.versionMin versionMin, m.verConversion verConversion
					FROM maps m
					INNER JOIN serveurs s ON m.idServOrigine = s.id
					WHERE m.etat = 'disponible'
					ORDER BY m.points DESC, m.id DESC LIMIT 5
					") or die("SQL error 49");
while ($donnees = $reponse->fetch()) {
    afficherLigneMap($donnees);
}
$reponse->closeCursor();
?>
		<tr class="ligneSupplementaire">
			<td colspan="3"><a rel="nofollow" href="maplist.php">More maps...</a>
				&middot; <a href="doc/add-map.php">Add my parkour map</a></td>
		</tr>
	</table>
</section>
<section class="texte" style="padding-top: 0">
	<!-- 
<h2>Last 7 days in CreativeParkour</h2>
<p style="margin-top:5px"><em>Statistics from Minecraft servers running the plugin around the world.</em><br />
<?php
/*
 * $reponse = $bdd->query ( "SELECT element, valeur FROM statsSemaine" ) or die ( "SQL error 9" );
 * while ( $donnees = $reponse->fetch () ) {
 * $statistiques [$donnees ["element"]] = $donnees ["valeur"];
 * }
 * $reponse->closeCursor ();
 *
 * TODO R�activer CRON
 *
 * echo '&nbsp;&bull; Parkours played: ' . htmlspecialchars($statistiques["parkoursTentes"]) . '<br />';
 * echo '&nbsp;&bull; Minutes played: ' . ceil(htmlspecialchars($statistiques["secondesJouees"] / 60)) . '<br />';
 * echo '&nbsp;&bull; Number of jumps: ' . htmlspecialchars($statistiques["nbSauts"]);
 */
?>
</p>-->

	<h2 style="margin-bottom: 0">Search</h2>
	<script>
  (function() {
    var cx = '005718124232212244920:kmntw_rghqw';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//cse.google.com/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  })();
</script>
	<div class="gcse-search"></div>
</section>
<?php include_once 'includes/bas.php'; ?>