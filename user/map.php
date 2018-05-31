<?php
require_once '../includes/haut-global.php';
verifLogin ();
require_once 'traitement-map.php';
$titre = "Map settings";
if ($infos ["nomMap"])
	$titre = htmlspecialchars ( $infos ["nomMap"] . " settings" );
require_once '../includes/haut-html.php';

if (! $infos) {
	header ( "Location: index.php" );
	die ( "Redirecting..." );
} else {
	?>
<section class="formulaire">
	<h1><?php echo $titre; ?></h1>
	<p>
		On this page, you can edit the settings of your map called <em><?php echo htmlspecialchars($infos["nomMap"]); ?></em>.
		Everyone will see them on the website (except the server name if it is
		private).<?php
	if ($infos ["etat"] === "disponible") {
		echo ' <a href="map.php?id=' . htmlspecialchars ( $infos ["idMap"] ) . '">Click here to see the public map page</a>.';
	}
	?>
	</p>
	<table>
		<tr>
			<td>
				<form id="form"
					action="user/map.php?id=<?php echo htmlspecialchars($infos["idMap"]);?>"
					method="POST" enctype="multipart/form-data">
					<table class="align-form">
						<tr>
							<td class="gauche">Name:</td>
							<td class="droite"><em><?php echo htmlspecialchars($infos["nomMap"]); ?></em></td>
						</tr>
						<tr>
							<td class="gauche">Shared on:</td>
							<td class="droite"><em><?php echo htmlspecialchars($infos["dateAjout"]); ?></em></td>
						</tr>
						<tr>
							<td class="gauche">Server name:</td>
							<td class="droite"><em><?php echo htmlspecialchars($infos["nomServ"]); ?></em></td>
						</tr>
						<!--<tr>
							<td class="gauche">Downloads:</td>
							<td class="droite"><em><?php //echo htmlspecialchars($infos["nbTelechargements"]); ?></em></td>
						</tr>-->
					</table>
					<?php
	if ($infos ["image"]) {
		echo 'Screenshot:<br />
		<a href="images/maps/' . htmlspecialchars ( $infos ["image"] ) . '" class="highslide" onclick="return hs.expand(this)">
		<img src="images/maps/mini/' . htmlspecialchars ( $infos ["image"] ) . '" alt="' . htmlspecialchars ( $infos ["nomMap"] ) . ' screenshot" title="Click to enlarge" /></a><br />';
	}
	?>
	<table class="align-form">
						<tr>
							<td class="gauche"><label for="image"><?php echo $infos["image"] ? "New screenshot" : "Screenshot"; ?>:</label></td>
							<td class="droite"><input type="file" name="image" id="image"></td>
						</tr>
					</table>

					<label for="description">Short description: </label><br />
					<textarea id="description" name="description" maxlength="600"
						rows="6" cols="60"><?php echo htmlspecialchars($_POST["description"] ? $_POST["description"]: $infos["description"]); ?></textarea>
					<br /> <br />
					<?php if ($infos["etat"] === "disponible") { ?>
					<input type="checkbox" id="supprimer" name="supprimer" value="Oui" />
					<label for="supprimer">Delete this map from the website (people
						will no longer be able to download it) </label> <br /> <br />
					<?php
	} else if ($infos ["etat"] === "creation") {
		if ($autresMaps) {
			?>
					<label for="ancienneMap">If this map is one of your old shared maps
						that you edited, please select the old map below if you want to
						remove it from the map list:</label><br /> <select
						name="ancienneMap" id="ancienneMap">
						<option value="false"></option>
						<?php
			foreach ( $autresMaps as $idAutreMap => $nomAutreMap ) {
				echo '<option value="' . htmlspecialchars ( $idAutreMap ) . '">' . htmlspecialchars ( $nomAutreMap ) . '</option>';
			}
			?>
			</select><br /> <br />
		<?php
		}
	}
	boutonForm ( "form", $infos ["etat"] === "creation" ? "Save and share this map" : "Save", $infos ["etat"] === "creation" ? true : false );
	?>
					<input type="hidden" name="save" value="save">
				</form>
			</td>
		</tr>
	</table>
	<?php
	if ($infos ["etat"] === "creation") {
		echo '<div class="avertissement">Any map that contains offensive buildings will be deleted and its creator can be blocked.</div>';
	}
	?>
</section>

<?php
}
include_once '../includes/bas.php';
?>