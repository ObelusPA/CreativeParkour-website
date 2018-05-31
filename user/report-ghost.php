<?php
require_once '../includes/haut-global.php';
verifLogin ();
require_once 'traitement-report-ghost.php';
$titre = "Ghost report";
require_once '../includes/haut-html.php';

if (! $infos) {
	header ( "Location: ../" );
	die ( "Redirecting..." );
} else if (! $fantomes) {
	$_SESSION ["erreurs"] [] = "There is no ghost for this map, maybe they have been deleted.";
	header ( "Location: ../" );
	die ( "Redirecting..." );
} else {
	?>
<section class="formulaire">
	<h1><?php echo $titre; ?></h1>
	<p>
		Select the ghost you want to report below for the map <strong><em><?php echo htmlspecialchars($infos["nomMap"]); ?></em></strong>.
		This tool must be used <strong>only</strong> if the ghost is obviously
		cheating in the parkour (flying, teleporting, hacks...). Any fake
		report can be punished.
	</p>
	<table>
		<tr>
			<td>
				<form id="form"
					action="user/report-ghost.php?id=<?php echo htmlspecialchars($infos["reportID"]);?>"
					method="POST">
					<div class="colonnes">
						<?php
	foreach ( $fantomes as $f ) {
		$id = htmlspecialchars ( "f" . $f ["id"] );
		echo '<input type="radio" name="fantome" value="' . htmlspecialchars ( $f ["id"] ) . '" id="' . $id . '" ' . ($_POST ["fantome"] == $f ["id"] ? "checked" : "") . '> <label for="' . $id . '">' . htmlspecialchars ( getNomMC ( $f ["uuidJoueur"] ) ) . " (" . htmlspecialchars ( ( int ) ($f ["ticks"] * 0.05) ) . "&nbsp;sec)</label><br />";
	}
	?>
					</div>
					<br /> <label for="raison">Additional information (why you report
						this ghost):</label><br /> <input type="text" name="raison"
						id="raison" maxlength="255"
						value="<?php echo htmlspecialchars($_POST["raison"]); ?>"><br /> <br />
						
					<?php boutonForm("form", "Send report", true); ?>
					<input type="hidden" name="send" value="send">
				</form>
			</td>
		</tr>
	</table>
</section>

<?php
}
include_once '../includes/bas.php';
?>