<?php
require_once '../includes/haut-global.php';
verifLogin ();

if ($_GET ["serv"]) {
	$reponse = $bdd->prepare ( "SELECT idsProprietaires, nom FROM serveurs WHERE id = :id ORDER BY id DESC LIMIT 1" );
	$reponse->execute ( array (
			'id' => $_GET ["serv"] 
	) ) or die ( "SQL error 7" );
	$infosServ = $reponse->fetch ();
	$reponse->closeCursor ();
}

if (! $infosServ || ! in_array ( $_SESSION ["utilisateur"]->id, explode ( ";", $infosServ ["idsProprietaires"] ) )) {
	http_response_code ( 403 );
	$_SESSION ["erreurs"] [] = "Unauthorized";
	header ( "Location: index.php" );
	die ( "Redirecting..." );
} else {
	
	$titre = "Cheaters (" . htmlspecialchars ( $infosServ ["nom"] ) . ")";
	require_once '../includes/haut-html.php';
	?>
<section class="texte">
	<h1><?php echo htmlspecialchars($titre); ?></h1>
	<p>Player ghosts in downloaded or shared parkour maps are sent to this
		website in order to appear in leaderboards, but they pass through an
		anti-cheat system before. Here are listed players from your server
		that are considered as cheaters in CreativeParkour. Their ghosts do
		not appear in leaderboards. This is only to inform you, you are free
		to do whatever you want with this data.</p>

	<script>
<!--
function majListe(html) {
	document.getElementById("listeFantomes").innerHTML = html;
}
function majListeIgnores(html) {
	document.getElementById("ignoredPlayers").innerHTML = html;
}
function majParams(html) {
	document.getElementById("notifSettings").innerHTML = html;
}
function afficherCaches() {
	return document.getElementById("afficherCaches").checked;
}
function afficherListe() {
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			majListe(this.responseText);
		}
	};
	xmlhttp.open("GET","user/requetes-cheaters.php?serv=<?php echo htmlspecialchars($_GET["serv"]); ?>&afficherCaches=" + (afficherCaches() ? 1 : 0), true);
	xmlhttp.send();
}

function afficherListeIgnores() {
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			majListeIgnores(this.responseText);
		}
	};
	xmlhttp.open("GET","user/requetes-cheaters.php?serv=<?php echo htmlspecialchars($_GET["serv"]); ?>&donnees=ignores", true);
	xmlhttp.send();
}

function afficherParams() {
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			majParams(this.responseText);
		}
	};
	xmlhttp.open("GET","user/requetes-cheaters.php?serv=<?php echo htmlspecialchars($_GET["serv"]); ?>&donnees=params", true);
	xmlhttp.send();
}

function cacherFantome(sel) {
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			majListe(this.responseText);
		}
	};
	xmlhttp.open("GET","user/requetes-cheaters.php?serv=<?php echo htmlspecialchars($_GET["serv"]); ?>&afficherCaches=" + (afficherCaches() ? 1 : 0) + "&hide=" + sel, true);
	xmlhttp.send();
}

function ignorerJoueur(sel) {
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			majListe(this.responseText);
			afficherListeIgnores();
		}
	};
	xmlhttp.open("GET","user/requetes-cheaters.php?serv=<?php echo htmlspecialchars($_GET["serv"]); ?>&afficherCaches=" + (afficherCaches() ? 1 : 0) + "&ignorePlayer=" + sel, true);
	xmlhttp.send();
}

function designorerJoueur(uuid) {
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			majListeIgnores(this.responseText);
			afficherListe();
		}
	};
	xmlhttp.open("GET","user/requetes-cheaters.php?serv=<?php echo htmlspecialchars($_GET["serv"]); ?>&donnees=ignores&uuidJoueur=" + uuid, true);
	xmlhttp.send();
}

function reglagesNotifs() {
	if (document.getElementById("settingsUpdated") != null)
		document.getElementById("settingsUpdated").remove();
	
	notifsJeu = document.getElementById("notifsJeu").checked;
	notifsMail = document.getElementById("notifsMail").checked;

	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			majParams(this.responseText);
		}
	};
	xmlhttp.open("GET","user/requetes-cheaters.php?serv=<?php echo htmlspecialchars($_GET["serv"]); ?>&donnees=params&notifsJeu=" + notifsJeu + "&notifsMail=" + notifsMail, true);
	xmlhttp.send();
}
//-->
</script>
	<style>
<!--
td.image {
	height: 100%;
}

td.image img {
	height: 100%;
}
-->
</style>
	<div class="optionsTableau">
		<input id="afficherCaches" type="checkbox" onchange="afficherListe()"><label
			for="afficherCaches">Show hidden ghosts</label>
	</div>
	<table class="tableau" style="font-size: 0.9em;">
		<thead>
			<tr>
				<th>Player</th>
				<th>Map</th>
				<th>Time</th>
				<th><?php echo infobulle("Ghost ID", 'Type "/cp ghost w <ghost ID>" to directly watch this ghost in game.'); ?></th>
				<th>Date</th>
				<th>Course analysis</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody id="listeFantomes">
			<tr>
				<td colspan="100%"><em>Loading...</em></td>
			</tr>
		</tbody>
	</table>
	<!-- <div class="optionsTableau">
		<?php echo fauxLienAction ( "cacherFantomesCoches()", "❌Hide", "Mark selected ghosts as \"checked\" and hide them from this list" ); ?>
		<img class="flecheAngle" src="images/flèche-angle.png" alt="↵">
	</div>-->

	<h2>Useful CreativeParkour commands</h2>
	<p>
		<em>Click them for a full description.</em>
	</p>
	<h3 style="margin-top: 10px;">Ghost commands</h3>
	<ul>
		<li><a href="doc/commands.php#ghost watch" class="cmd">/cp ghost watch
				&#60;ghost&nbsp;ID&#62;</a>: adds the specified ghost to your ghost
			selection (type an ID from the table above). It also teleports you to
			the right map if you are not already in. Alias: <span class="cmd">/cp
				ghost w &#60;ghost&nbsp;ID&#62;</span>.</li>
		<li><a href="doc/commands.php#ghost play" class="cmd">/cp ghost play</a>:
			starts playing the ghosts you selected. Alias: <span class="cmd">/cp
				ghost p</span>.</li>
		<li><a href="doc/commands.php#ghost speed" class="cmd">/cp ghost speed
				[multiplier]</a>: makes ghosts you are watching going faster. Alias:
			<span class="cmd">/cp ghost s [multiplier]</span>.</li>
		<li><a href="doc/commands.php#ghost rewind" class="cmd">/cp ghost
				rewind [seconds]</a>: puts ghosts 10 seconds in the past (or the
			specified number of seconds). Alias: <span class="commande">/cp ghost
				r [seconds]</span></li>
		<li><a href="doc/commands.php#ghost moment" class="cmd">/cp ghost
				moment [second]</a>: puts ghosts at the specified second. Alias: <span
			class="commande">/cp ghost m [second]</span></li>
	</ul>
	<h3 style="margin-top: 10px;">General commands</h3>
	<ul>
		<li><a href="doc/commands.php#removetime" class="cmd">/cp removetime
				&#60;player&#62;</a>: deletes player's time in the map where you
			are.</li>
		<li><a href="doc/commands.php#removetime" class="cmd">/cp removetime
				&#60;player&#62; all</a>: deletes all the player's times, in all the
			maps.</li>
		<li><a href="doc/commands.php#ban" class="cmd">/cp ban
				&#60;player&#62;</a>: Prohibits the player to use CreativeParkour.</li>
		<li><a href="doc/commands.php#pardon" class="cmd">/cp pardon
				&#60;player&#62;</a>: Allows the player to use CreativeParkour.</li>
	</ul>

	<h2>Manage ignored players</h2>
	<p>
		Click players you want to show again in the cheater list.<br /> <em><?php echo fauxLienAction("affichageBloc('ignoredPlayers')", "Click here to show the list of players you ignored."); ?></em>
	</p>
	<p id="ignoredPlayers" class="colonnes" style="display: none;"></p>

	<h2 id="notifications">Notification settings</h2>
	<?php
	if (! mailVerifie ( $_SESSION ["utilisateur"]->id )) {
		echo '<br />';
		avertissementMail ();
	}
	?>
	<p id="notifSettings"></p>
</section>

<script>
<!--
afficherListe();
afficherListeIgnores();
afficherParams();
//-->
</script>
<?php
}
include_once '../includes/bas.php';
?>