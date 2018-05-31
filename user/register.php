<?php
require_once '../includes/haut-global.php';
verifLogin ( "To register, log in with a social network or write your email and choose a password on the right." );
require_once 'traitement-register.php';
$titre = "Register";
require_once '../includes/haut-html.php';

if (connecte ()) {
	?>
<div class="sourire">
	<p>
		Welcome <?php echo htmlspecialchars($_SESSION["utilisateur"]->nom); ?>, you are registered! You can now <a
			href="doc/add-map.php">share your parkour maps</a> with <span
			class="commande">/cp share</span> and use other remote
		CreativeParkour features.
	</p>
</div>

<?php
}
include_once '../includes/bas.php';
?>