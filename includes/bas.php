<footer>
	<table>
		<tr>
			<td style="width: 30%;"><a href="" title="Main page"><img
					class="logo" alt="CreativeParkour's logo"
					src="images/logo-sombre.png"></a></td>
			<td><p>
					CreativeParkour Bukkit plugin by Obelus<br /> <a target="_blank"
						href="http://dev.bukkit.org/bukkit-plugins/creativeparkour/">Bukkit
						Dev</a> &middot; <a target="_blank"
						href="https://www.spigotmc.org/resources/creativeparkour.17303/">Spigot</a>
					&middot; <a target="_blank"
						href="http://www.curse.com/bukkit-plugins/minecraft/creativeparkour">Curse</a>
					&middot; <a target="_blank"
						href="https://github.com/ObelusPA/CreativeParkour">GitHub</a><br />
					<a target="_blank"
						href="https://www.paypal.com/cgi-bin/webscr?hosted_button_id=ACM6NFLG73YYW&item_name=CreativeParkour&cmd=_s-xclick">Donate</a><br />
					<br /> Thanks <a target="_blank" href="https://crafatar.com">Crafatar</a>
					for providing player avatars.
				</p></td>
			<td>Any question or idea?<br /> <a href="contact.php">Contact the
					author</a><br /> <a target="_blank"
				href="https://github.com/ObelusPA/CreativeParkour/issues">Report a
					bug</a>
			</td>
		</tr>
	</table>
</footer>

</div>


<script src='https://www.google.com/recaptcha/api.js' async defer></script>
<script type="text/javascript" src="highslide/highslide-with-gallery.js"></script>
<link rel="stylesheet" type="text/css" href="highslide/highslide.css" />
<script type="text/javascript">
	hs.align = 'center';
	hs.transitions = ['expand', 'crossfade'];
	hs.wrapperClassName = 'dark borderless floating-caption';
	hs.fadeInOut = true;
	hs.dimmingOpacity = .75;
</script>

<link rel="stylesheet" type="text/css"
	href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css" />
<script async
	src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.js"></script>
<script>
window.addEventListener("load", function(){
window.cookieconsent.initialise({
  "palette": {
    "popup": {
      "background": "#1a1a1a",
      "text": "#e0e0e0"
    },
    "button": {
      "background": "#ffc005"
    }
  },
  "theme": "edgeless",
  "position": "bottom-right",
  "content": {
    "href": "https://creativeparkour.net/cookie-policy.php"
  }
})});
</script>

<!-- Scripts perso -->
<script>
<!--
// Affiche/masque l'élément id
function affichageBloc(id) {
	var affiche = document.getElementById(id).style.display != "none";
	document.getElementById(id).style.display=(affiche ? "none" : "block")
}
//-->
</script>
</body>
</html>
<?php
// Ajout de la connexion actuelle à la table
$req = $bdd->prepare("INSERT INTO connexions SET date = NOW(), idUtilisateur = :idUtilisateur, ip = :ip, infos = :infos, page = :page, codeHTTP = :codeHTTP, https = :https");
$req->execute(array(
    'idUtilisateur' => $_SESSION["utilisateur"] ? $_SESSION["utilisateur"]->id : 0,
    'ip' => getUserIP(),
    'infos' => $_SERVER['HTTP_USER_AGENT'],
    'page' => $_SERVER['REQUEST_URI'],
    'codeHTTP' => http_response_code(),
    'https' => $_SERVER['HTTPS'] ? 1 : 0
)) or die("SQL error 60");
$req->closeCursor();
?>