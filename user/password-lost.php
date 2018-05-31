<?php
require_once '../includes/haut-global.php';
if (connecte ()) {
	retournerOuHeader ( "../" );
} else {
	require_once 'traitement-password-lost.php';
	$titre = "Password lost";
	$noIndex = true;
	require_once '../includes/haut-html.php';
	?>
<section class="formulaire">
	<h1><?php echo $titre; ?></h1>
	<p>If you have forgotten your password, you can use this form to reset
		it. You will receive an email with instructions.</p>
	<table>
		<tr>
			<td>
				<form id="form" action="user/password-lost.php" method="POST">
					<label for="mail">Email:</label> <input type="email" name="mail"
						id="mail" required /><br /> <br />
					<?php boutonForm("form", "Reset password", true); ?>
					<input type="hidden" name="reset" value="reset">
				</form>
			</td>
		</tr>
	</table>
</section>

<?php
}
include_once '../includes/bas.php';
?>