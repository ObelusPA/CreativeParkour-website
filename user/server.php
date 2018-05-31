<?php
require_once '../includes/haut-global.php';
verifLogin ();
require_once 'traitement-server.php';
$titre = "Server management";
require_once '../includes/haut-html.php';

if (! $infos) {
	header ( "Location: index.php" );
	die ( "Redirecting..." );
} else {
	?>
<script>
<!--
function chgmtLocalhost(checked, radio) {
	document.getElementById("siteWeb").disabled = checked;
	document.getElementById("description").disabled = checked;
	document.getElementById("description").required = !checked;
	if (radio) {
		document.getElementById("ip").disabled = checked;
		document.getElementById("localhost").checked = checked;
		document.getElementById("public").disabled = checked;
	}
	
    if (checked) {
		document.getElementById("siteWeb").value = "";
		document.getElementById("description").innerHTML = "";
		document.getElementById("siteWeb").placeholder = "Useless for private servers";
		document.getElementById("description").placeholder = "Useless for private servers";
		if (radio) {
			document.getElementById("ip").value="localhost";
			document.getElementById("public").title = "Localhost servers are private";
			document.getElementById("privé").checked = true;
		}
    }
    else {
		document.getElementById("siteWeb").removeAttribute("placeholder");
		document.getElementById("description").removeAttribute("placeholder");
		if (radio) {
	    	document.getElementById("ip").value = "";
			document.getElementById("ip").focus();
			document.getElementById("public").removeAttribute("title");
			document.getElementById("public").checked = true;
		}
    }
}
//-->
</script>
<section class="formulaire">
	<h1><?php echo htmlspecialchars($titre); ?></h1>
	<p>
		On this page, you can see and edit your server information that will
		be displayed (if you want to) on the website, for example next to
		parkour maps created on your server.<br /> These information are used
		to avoid spam and as always, they will <strong>never</strong> exit
		this website. You can choose to display them publicly or not using the
		checkboxes at the bottom.<br />Showing your server information can be
		a good way to encourage players to join your server!<?php
	if ($infos ["etat"] === "public") {
		echo ' <br /><a href="server.php?id=' . htmlspecialchars ( $infos ["id"] ) . '">Click here to see the public server page</a>.';
	}
	?>
	</p>
	<table>
		<tr>
			<td>
				<form
					action="user/server.php?id=<?php echo htmlspecialchars($infos["id"]);?>"
					method="POST">
					<table class="align-form">
						<tr>
							<td class="gauche"><label for="ip">Server IP:</label></td>
							<td class="droite"><input type="text" id="ip" name="ip"
								maxlength="255"
								value="<?php echo htmlspecialchars($_POST["ip"] ? $_POST["ip"]: $infos["ip"]); ?>"
								required
								onkeyup='if (this.value.toLowerCase() === "localhost") { chgmtLocalhost(true, true); }' />
								<input type="checkbox" id="localhost" name="localhost"
								value="Oui" onchange="chgmtLocalhost(this.checked, true)" /> <label
								for="localhost">Localhost</label></td>
						</tr>
						<tr>
							<td class="gauche"><label for="nom">Server name: </label></td>
							<td class="droite"><input type="text" id="nom" name="nom"
								value="<?php echo htmlspecialchars($_POST["nom"] ? $_POST["nom"]: $infos["nom"]); ?>"
								maxlength="255" required /></td>
						</tr>
						<tr>
							<td class="gauche"><label for="siteWeb">Server website <span
									class="optional">(optional)</span>:
							</label></td>
							<td class="droite"><input type="url" id="siteWeb" name="siteWeb"
								maxlength="255"
								value="<?php echo htmlspecialchars($_POST["siteWeb"] ? $_POST["siteWeb"]: $infos["siteWeb"]); ?>" /></td>
						</tr>
					</table>
					<label for="description">Short description: </label><br />
					<textarea id="description" name="description" maxlength="800"
						rows="8" cols="60" required><?php echo htmlspecialchars($_POST["description"] ? $_POST["description"]: $infos["description"]); ?></textarea>
					<br />
					<div class="cases">
						<input type="radio" id="public" name="public" value="Yes"
							onchange="chgmtLocalhost(false, false)"
							<?php if ($_POST["public"] === "Yes" || $infos["etat"] === "public") { echo "checked"; }?> />
						<label for="public">I want these information to be displayed on
							this website.</label><br /> <input type="radio" id="privé"
							name="public" value="No" onchange="chgmtLocalhost(true, false)"
							<?php if ($_POST["public"] === "No" || $infos["etat"] === "prive") { echo "checked"; }?> />
						<label for="privé">This is a private server, I want information
							like its IP to remain secret and not be displayed.</label>
						<br />
					</div>
					<input type="submit" name="save" value="Save" />
				</form>
			</td>
		</tr>
	</table>
</section>
<script>
<!--
//Première vérification si le champ est déjà rempli
if (document.getElementById("ip").value.toLowerCase() === "localhost") { chgmtLocalhost(true, true); }
if (document.getElementById("privé").checked) { chgmtLocalhost(true, false); }
//-->
</script>

<?php
}
include_once '../includes/bas.php';
?>