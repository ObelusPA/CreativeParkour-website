<?php
require_once 'includes/haut-global.php';

// Récupération des infos sur le serveur
$reponse = $bdd->prepare ( "SELECT id, ip, idsProprietaires, nom, description, siteWeb FROM serveurs WHERE id = :id AND etat = 'public' ORDER BY id DESC LIMIT 1" );
$reponse->execute ( array (
		'id' => $_GET ["id"] 
) ) or die ( "SQL error 8" );
$infos = $reponse->fetch ();
$reponse->closeCursor ();

if (! $infos) {
	header ( "Location: /" );
	die ( "Redirecting..." );
}

// Recherche des maps pour ce serveur
$reponse = $bdd->prepare ( "SELECT id, nom FROM maps WHERE etat = 'disponible' AND idServOrigine = :idServ" );
$reponse->execute ( array (
		"idServ" => $infos ["id"] 
) ) or die ( "SQL error 16" );
$nbMaps = 0;
while ( $donnees = $reponse->fetch () ) {
	$infosMaps [] = $donnees;
	$nbMaps ++;
}
$reponse->closeCursor ();

if (! $infosMaps) {
	$_SESSION ["erreurs"] [] = "Nobody on this server shared a parkour map.";
	header ( "Location: /" );
	die ( "Redirecting..." );
}

$titre = htmlspecialchars ( $infos ["nom"] );
$suffixeTitre = "servers";
$sMaps = "s";
if ($nbMaps == 1)
	$sMaps = "";
$metaDescription = "\"" . htmlspecialchars ( $infos ["nom"] ) . "\" is a Minecraft server where you can play parkour maps created with the CreativeParkour Bukkit plugin. " . htmlspecialchars ( $nbMaps ) . " parkour map" . $sMaps . " have been created on this server!";
require_once 'includes/haut-html.php';
?>
<section class="descriptionMap">
	<h1>Server: <?php echo htmlspecialchars($titre); ?></h1>
	<p class="margeGauche">This server has the CreativeParkour Bukkit plugin.</p>
	<table>
		<tr>
			<td><strong>Name</strong>: <?php echo htmlspecialchars ( $infos["nom"] ); ?><br />
				<strong>IP</strong>: <?php echo htmlspecialchars ( $infos["ip"] ); ?><br />
				<?php
				if ($infos ["siteWeb"]) {
					echo '<strong>Website</strong>: <a target="_blank" href="' . htmlspecialchars ( $infos ["siteWeb"] ) . '">' . htmlspecialchars ( $infos ["siteWeb"] ) . '</a><br />';
				}
				?>
				<strong>CreativeParkour maps created on this server:</strong><br />
				<ul>
					<?php
					foreach ( $infosMaps as $map ) {
						echo '<li><a href="map.php?id=' . htmlspecialchars ( $map ["id"] ) . '">' . htmlspecialchars ( $map ["nom"] ) . '</a></li>';
					}
					?>
				</ul></td>
			<td class="varianteFond" style="width: 50%">
				<?php
				echo '<strong>Description</strong>:<br />' . nl2br ( htmlspecialchars ( $infos ["description"] ) );
				
				if (in_array ( $_SESSION ["utilisateur"]->id, explode ( ";", $infos ["idsProprietaires"] ) )) {
					echo '<br /><br /><strong><a href="user/server.php?id=' . htmlspecialchars ( $infos ["id"] ) . '">Edit server settings</a></strong>';
				}
				?>
			</td>
		</tr>
	</table>
</section>
<?php include_once 'includes/bas.php'; ?>