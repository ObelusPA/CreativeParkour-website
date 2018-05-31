<?php
require_once '../includes/haut-global.php';
if (connecte ()) {
	header ( "Location: logout.php" );
	die ();
} else {
	require_once 'traitement-password-reset.php';
	$titre = "Password reset";
	$noIndex = true;
	require_once '../includes/haut-html.php';
	
	if ($infos) { // En principe c'est déjà vérifié dans traitement-password-reset.php
		?>
<section class="formulaire">
	<h1><?php echo $titre; ?></h1>
	<p>Welcome back <?php echo htmlspecialchars($infos["nom"])?>, please enter your new password below:</p>
	<table>
		<tr>
			<td>
				<form id="form"
					action="user/password-reset.php?u=<?php echo htmlspecialchars($_GET["u"]); ?>&t=<?php echo htmlspecialchars($_GET["t"]); ?>"
					method="POST">
					<table class="align-form">
						<tr>
							<td class="gauche"><label for="mdp">Password:</label></td>
							<td class="droite"><input type="password" name="mdp" id="mdp"
								required /></td>
						</tr>
						<tr>
							<td class="gauche"><label for="mdpC">Confirm password:</label></td>
							<td class="droite"><input type="password" name="mdpC" id="mdpC"
								required /></td>
						</tr>
					</table>
					<?php boutonForm("form", "Change password", true); ?>
					<input type="hidden" name="reset" value="reset" />
				</form>
			</td>
		</tr>
	</table>
</section>

<?php
	}
}
include_once '../includes/bas.php';
?>