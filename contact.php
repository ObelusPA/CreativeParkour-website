<?php
require_once 'includes/haut-global.php';

// Traitement
if ($_POST ["send"] === "send") {
	if (! verifReCaptcha ())
		$_SESSION ["erreurs"] [] = "You did not complete the CAPTCHA verification properly. Please try again.";
	else if ($_SESSION ["dernierMail"] && $_SESSION ["dernierMail"] + 120 > time ())
		$_SESSION ["erreurs"] [] = "Please wait before sending a message again";
	else if (! $_SESSION ["utilisateur"]->mail && ! filter_var ( $_POST ["mail"], FILTER_VALIDATE_EMAIL ))
		$_SESSION ["erreurs"] [] = "Please enter a valid email";
	if (strlen ( $_POST ["message"] ) < 20)
		$_SESSION ["erreurs"] [] = "Your message is too short";
	if (strlen ( $_POST ["message"] ) > 10000)
		$_SESSION ["erreurs"] [] = "Your message is too long";
	
	if (! $_SESSION ["erreurs"]) {
		$titre = "Nouveau message";
		if ($_SESSION ["utilisateur"])
			$titre = "Nouveau message de " . htmlspecialchars ( $_SESSION ["utilisateur"]->nom );
		$expediteur = $_SESSION ["utilisateur"]->mail;
		if (! $expediteur)
			$expediteur = $_POST ["mail"];
		$message = "<h2>Informations</h2><strong>Expéditeur : " . htmlspecialchars ( $expediteur ) . "</strong><br /><strong>IP : " . htmlspecialchars ( getUserIP () ) . "</strong><br /><br /><h2>Message</h2>";
		envoyerMail ( "obelus@creativeparkour.net", $titre, $message . nl2br ( htmlspecialchars ( $_POST ["message"] ) ), $expediteur );
		$_SESSION ["dernierMail"] = time ();
		$_SESSION ["msgOK"] [] = "Your message has been sent, thank you!";
		retournerOuHeader ( "/" );
	}
}

$titre = "Contact the author";
$noIndex = true;
require_once 'includes/haut-html.php';
?>
<section class="formulaire">
	<h1>Send a message</h1>
	<p>
		You can use this form to send a message in English or in French to
		CreativeParkour's author if you have a question, if you need help, if
		you have a suggestion, or for whatever reason.<br /> You can also
		leave a message on CreativeParkour's <a target="_blank"
			href="http://dev.bukkit.org/bukkit-plugins/creativeparkour/">Bukkit</a>
		or <a target="_blank"
			href="https://www.spigotmc.org/resources/creativeparkour.17303/">Spigot</a>
		page.
	</p>
	<table>
		<tr>
			<td>
				<form id="form" action="contact.php" method="POST"
					enctype="multipart/form-data">
							<?php if (!$_SESSION["utilisateur"]->mail) { ?>
					<table class="align-form">
						<tr>
							<td class="gauche"><label for="mail">Your email (only to answer
									you):</label></td>
							<td class="droite"><input type="email" name="mail" id="mail"
								maxlength="255"
								value="<?php echo htmlspecialchars($_POST["mail"] ? $_POST["mail"] : ""); ?>"
								required /></td>
						</tr>
					</table>
						<?php
							} else {
								echo "An answer will be sent to <em>" . htmlspecialchars ( $_SESSION ["utilisateur"]->mail ) . "</em>.<br />";
							}
							?>
					<label for="message">Your message: </label><br />
					<textarea id="message" name="message" rows="12" cols="70"
						maxlength="10000" required><?php echo htmlspecialchars($_POST["message"] ? $_POST["message"]: ""); ?></textarea>
					<br />
					<?php boutonForm("form", "Send", true); ?>
					<input type="hidden" name="send" value="send">
				</form>
			</td>
		</tr>
	</table>
</section>
<?php include_once 'includes/bas.php'; ?>