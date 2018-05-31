<?php
require_once '../includes/haut-global.php';
if (connecte ()) {
	retournerOuHeader ( "../" );
} else {
	require_once 'traitement-signup.php';
	$titre = "Sign up";
	require_once '../includes/haut-html.php';
	
	if (! $_SESSION ["cle"]) { // Si le mec n'a pas été envoyé par le plugin, il dégage
		$_SESSION ["erreurs"] [] = "You have to play CreativeParkour on a server and be asked to sign up on this website.";
		header ( "Location: ../" );
	}
	?>
<section class="formulaire">
	<h1>Sign up</h1>
	<table>
		<tr>
				<?php if (!$utilisateurSocial) { ?>
					<th>Sign up with a social network…</th>
			<th>…or create a new account</th>
		</tr>
		<tr>
			<td><a href="user/login.php?provider=facebook"><img
					alt="Log in with Facebook" src="images/login-facebook.png"
					class="social-login-img"></a><br /> <a
				href="user/login.php?provider=twitter"><img
					alt="Log in with Twitter" src="images/login-twitter.png"
					class="social-login-img"></a><br /> <a
				href="user/login.php?provider=google"><img alt="Log in with Google"
					src="images/login-google.png" class="social-login-img"></a><br /> <a
				href="user/login.php?provider=discord"><img
					alt="Log in with Discord" src="images/login-discord.png"
					class="social-login-img"></a></td>
				<?php } ?>
				<td>
				<form id="form" action="user/signup.php" method="POST">
					<table class="align-form">
							<?php if (!$mailConnu) { ?>
								<tr>
							<td class="gauche"><label for="mail">Email:</label></td>
							<td class="droite"><input type="email" name="mail" id="mail"
								maxlength="255"
								value="<?php echo htmlspecialchars($_POST["mail"] ? $_POST["mail"] : $nu->mail); ?>"
								required /></td>
						</tr>
							<?php } ?>
							<tr>
							<td class="gauche"><label for="nomMC">Minecraft name:</label></td>
							<td class="droite"><input type="text" id="nomMC"
								value="<?php echo htmlspecialchars($_SESSION["nomMC"]); ?>"
								required disabled /></td>
						</tr>
						<tr>
							<td class="gauche"><label for="mdp">Password<?php if ($utilisateurSocial) echo ' <span class="optional">(optional)</span>'; ?>:</label></td>
							<td class="droite"><input type="password" name="mdp" id="mdp"
								<?php if (!$utilisateurSocial) echo "required "; ?> /></td>
						</tr>
						<tr>
							<td class="gauche"><label for="mdpC">Confirm password<?php if ($utilisateurSocial) echo ' <span class="optional">(optional)</span>'; ?>:</label></td>
							<td class="droite"><input type="password" name="mdpC" id="mdpC"
								<?php if (!$utilisateurSocial) echo "required "; ?> /></td>
						</tr>
						<!--<tr>
							<td class="gauche"><label for="timezone">Time zone:</label></td>
							<td class="droite"><select id="timezone" name="timezone">
							<?php
	/*
	 * $timezone_identifiers = DateTimeZone::listIdentifiers ();
	 * for($i = 0; $i < count ( $timezone_identifiers ); $i ++) {
	 * echo '<option id="' . htmlspecialchars ( $timezone_identifiers [$i] ) . '" value="' . htmlspecialchars ( $timezone_identifiers [$i] ) . '">' . htmlspecialchars ( $timezone_identifiers [$i] ) . '</option>';
	 * }
	 */
	?>
							</select></td>
						</tr>-->
					</table>
						<?php if ($utilisateurSocial) echo '<span class="optional">If you choose a password, you will be able to log in using it and your account will be more secure. If not, you will only be able to login with your social network account.</span><br />'; ?>
						<br />
					<!--<input type="checkbox" id="règles" name="règles" value="Oui" required /> <label
						for="règles">I agree with the <a href="../rules.php"
						target="_blank">CreativeParkour online services rules</a></label><br />
					<br />-->
					<?php boutonForm("form", "Sign up", true); ?>
					<input type="hidden" name="signup" value="signup">
				</form>
			</td>
		</tr>
	</table>
	<p>
		Your personal information will <strong>never</strong> be publicly
		displayed or sell to any company.<br /> Your social profiles will <strong>never</strong>
		be used to post content without your consent or send annoying
		invitations to your friends.
	</p>
</section>
<?php
}
include_once '../includes/bas.php';
?>					