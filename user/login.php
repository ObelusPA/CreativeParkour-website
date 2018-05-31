<?php
require_once '../includes/haut-global.php';
if (connecte ()) {
	retournerOuHeader ( "../" );
} else {
	require_once 'traitement-login.php';
	$titre = "Log in";
	$noIndex = true;
	require_once '../includes/haut-html.php';
	?>
<section class="formulaire">
	<h1>Log in</h1>
	<script type="text/javascript">
	<!--
	function cocherRemember(checked) {
		var x = document.getElementsByClassName("social-login-img");
		var i;
		for (i = 0; i < x.length; i++) {
		    if (checked) {
			    x[i].parentNode.href = x[i].parentNode.href + "&remember=1";
		    } else {
			    x[i].parentNode.href = x[i].parentNode.href.replace(/&remember=1/g,'');
		    }
		}
	}
	//-->
	</script>
	<table>
		<tr>
			<td><a href="user/login.php?provider=facebook&remember=1"><img
					alt="Log in with Facebook" src="images/login-facebook.png"
					class="social-login-img"></a><br /> <a
				href="user/login.php?provider=twitter&remember=1"><img
					alt="Log in with Twitter" src="images/login-twitter.png"
					class="social-login-img"></a><br /> <a
				href="user/login.php?provider=google&remember=1"><img
					alt="Log in with Google" src="images/login-google.png"
					class="social-login-img"></a><br /> <a
				href="user/login.php?provider=discord&remember=1"><img
					alt="Log in with Discord" src="images/login-discord.png"
					class="social-login-img"></a><br /> <input type="checkbox"
				id="remember1" checked onchange="cocherRemember(this.checked)" /> <label
				for="remember1">Remember me on this computer</label></td>
			<td>
				<form id="form" action="user/login.php" method="POST">
					<table class="align-form">
						<tr>
							<td class="gauche"><label for="mail">Name or email:</label></td>
							<td class="droite"><input type="text" name="mail" id="mail"
								value="<?php echo htmlspecialchars($_POST["mail"]);?>"
								maxlength="255" required /></td>
						</tr>
						<tr>
							<td class="gauche"><label for="mdp">Password:</label></td>
							<td class="droite"><input type="password" name="mdp" id="mdp"
								maxlength="64" /></td>
						</tr>
						<tr>
							<td style="padding: 0"></td>
							<td class="gauche" style="padding: 0"><a
								href="user/password-lost.php" style="font-size: 0.8em">Forgot
									your password?</a></td>
						</tr>
					</table>
					<input type="checkbox" id="remember2" name="remember" checked /> <label
						for="remember2">Remember me on this computer</label><br />
					<?php
	// reCAPTCHA si connexion ratée dans les 30 minutes
	$reponse = $bdd->prepare ( "SELECT nbTentatives FROM tentativesConnexion WHERE ip = :ip AND dernierEssai > DATE_SUB(NOW(), INTERVAL 30 MINUTE)" );
	$reponse->execute ( array (
			'ip' => getUserIP () 
	) ) or die ( "SQL error 64" );
	$donnees = $reponse->fetch ();
	if ($donnees) {
		$recaptcha = true;
	}
	$reponse->closeCursor ();
	
	boutonForm ( "form", "Log in", $recaptcha );
	?>
					 <input type="submit" name="signup" value="Create an account" /> <input
						type="hidden" name="login" value="login">
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