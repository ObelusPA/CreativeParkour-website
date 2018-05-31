<?php
require_once '../includes/haut-global.php';
verifLogin ();
require_once 'traitement-user.php';
$titre = htmlspecialchars ( $_SESSION ["utilisateur"]->nom );
require_once '../includes/haut-html.php';
?>
<section class="texte" style="padding-bottom: 0px;">
	<h1>User page and preferences</h1>
	<?php
	if ($mailNonVerifie) {
		avertissementMail ();
	}
	?>
	<table class="tableauFond">
		<thead>
			<tr>
				<th scope="col">Personal details</th>
				<th scope="col" class="varianteFond">Login preferences</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					Name: <?php echo htmlspecialchars($infos["nom"]); ?><br />
					Email: <?php echo htmlspecialchars($infosMail["adresse"]); ?> (<span
					class="fauxLien" onclick="affichageBloc('formMail')">change</span>)<br />
					<!-- Formulaire de changement de mail (caché au départ) -->
					<form action="user/index.php" method="POST"
						style="margin-bottom: 20px; display: <?php echo ($afficherChangementMail ? 'block' : 'none'); ?>" id="formMail">
						<table class="align-form">
							<tr>
								<td class="gauche"><label for="mail">New email:</label></td>
								<td class="droite"><input type="email" name="mail" id="mail"
									required /></td>
							</tr>
							<?php if ($infos["mdp"]) { ?>
							<tr>
								<td class="gauche"><label for="mdp">Your password:</label></td>
								<td class="droite"><input type="password" name="mdp" id="mdp"
									required /></td>
							</tr>
							<?php } ?>
						</table>
						<input type="submit" name="changerMail" value="Change email"
							style="display: block; margin: auto" />
					</form>
					
					Sign in date: <?php echo htmlspecialchars($infos["dateCreation"]); ?>
				</td>
				<td class="varianteFond" style="width: 50%">
				Password:
				<?php
				if ($infos ["mdp"]) {
					echo '<span class="fauxLien" onclick="affichageBloc(\'formPass\')">change</span>';
				} else {
					echo '<span class="texteInfo" style="color:red" title="You are using a social network to log in but you can create a password to make your account more secure and add another way to log in.">no password</span> (<span class="fauxLien" onclick="affichageBloc(\'formPass\')">create one</span>)';
				}
				echo '<br />';
				// Formulaire de changement de mot de passe
				?>
				<form action="user/index.php" method="POST"
						style="margin-bottom: 20px; display: <?php echo ($afficherChangementPass ? 'block' : 'none'); ?>" id="formPass">
						<table class="align-form">
						<?php if ($infos["mdp"]) { ?>
							<tr>
								<td class="gauche"><label for="mdpA">Current password:</label></td>
								<td class="droite"><input type="password" name="mdpA" id="mdpA"
									required /></td>
							</tr>
							<?php } ?>
							<tr>
								<td class="gauche"><label for="mdp">New password:</label></td>
								<td class="droite"><input type="password" name="mdp" id="mdp"
									required /></td>
							</tr>
							<tr>
								<td class="gauche"><label for="mdpC">Confirm new password:</label></td>
								<td class="droite"><input type="password" name="mdpC" id="mdpC"
									required /></td>
							</tr>
						</table>
						<input type="submit" name="changerPass"
							value="<?php echo htmlspecialchars($infos["mdp"] ? "Change password" : "Create password"); ?>"
							style="display: block; margin: auto" />
					</form>
				
				<?php
				function socialLoginActive($nom) {
					global $infos;
					echo ucfirst ( $nom ) . ' login: ';
					if ($infos [$nom . "ID"]) {
						echo '<span style="color:green">enabled</span>';
					} else {
						echo '<span style="color:red">disabled</span> (<a href="user/index.php?social=' . htmlspecialchars ( $nom ) . '">enable</a>)';
					}
				}
				
				socialLoginActive ( "facebook" );
				echo '<br />';
				socialLoginActive ( "twitter" );
				echo '<br />';
				socialLoginActive ( "google" );
				echo '<br />';
				socialLoginActive ( "discord" );
				?>
				<br />
				</td>
			</tr>
		</tbody>
	</table>


	<h2>Maps and server settings</h2>
	<p>Click them to change their settings.</p>
	<table class="tableauFond">
		<tr>
			<th scope="col">Your maps</th>
			<th scope="col" class="varianteFond">Your servers</th>
		</tr>
		<tr>
			<td>
	<?php
	$reponse = $bdd->prepare ( "SELECT id, nom FROM maps WHERE idCreateur = :idUtilisateur AND etat = 'disponible' ORDER BY id DESC" );
	$reponse->execute ( array (
			"idUtilisateur" => $_SESSION ["utilisateur"]->id 
	) ) or die ( "SQL error 82" );
	while ( $donnees = $reponse->fetch () ) {
		echo '&nbsp;&bull; <a href="/user/map.php?id=' . htmlspecialchars ( $donnees ["id"] ) . '">' . htmlspecialchars ( $donnees ["nom"] ) . '</a><br />';
		$map = true;
	}
	$reponse->closeCursor ();
	if (! $map) {
		echo '<em>You did not share any map. <a href="doc/add-map.php">Click here to learn how to do it</a>.</em>';
	}
	?>
	</td>
			<td class="varianteFond">
	<?php
	$reponse = $bdd->query ( "SELECT id, nom, etat, idsProprietaires FROM serveurs WHERE etat = 'public' OR etat = 'prive' ORDER BY id DESC" ) or die ( "SQL error 95" );
	while ( $donnees = $reponse->fetch () ) {
		if (in_array ( $_SESSION ["utilisateur"]->id, explode ( ";", $donnees ["idsProprietaires"] ) )) {
			$prive = "";
			if ($donnees ["etat"] == "prive")
				$prive = " (secret)";
			echo '&nbsp;&bull; <a href="/user/server.php?id=' . htmlspecialchars ( $donnees ["id"] ) . '">' . htmlspecialchars ( $donnees ["nom"] ) . '</a>' . $prive . '<br />';
			$serv = true;
		}
	}
	$reponse->closeCursor ();
	// Si aucun serveur n'a été trouvé, message
	if (! $serv) {
		echo '<em>You have not registered any CreativeParkour server. If you are an admin on a server that has the plugin, type <span class="commande">/cp config sharing</span> to do it.</em>';
	}
	?>
		</td>
		</tr>
	</table>
</section>

<?php
include_once '../includes/bas.php';
?>