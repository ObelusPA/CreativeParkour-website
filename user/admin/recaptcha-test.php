<?php
require_once '../../includes/haut-global.php';

// Traitement
if ($_POST["send"] === "send") {
    if (! verifReCaptcha())
        $_SESSION["erreurs"][] = "CAPTCHA invalide.";
        else
            $_SESSION["msgOK"][] = "CAPTCHA valide.";
}

$titre = "Administration";
require_once 'haut-admin.php';
?>
<section class="texte">
	<h1>Test ReCaptcha</h1>
	<form id="form" action="user/admin!/recaptcha-test.php" method="POST">
		<p>
			<?php boutonForm("form", "Tester", true); ?>
			<input type="hidden" name="send" value="send">
		</p>
	</form>
</section>
<?php include_once '../../includes/bas.php'; ?>