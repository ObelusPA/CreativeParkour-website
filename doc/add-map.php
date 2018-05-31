<?php
require_once '../includes/haut-global.php';
$titre = "Add a parkour map";
$suffixeTitre = "documentation";
$metaDescription = "Everyone can add fun parkour maps on this website and share them with all the world using the CreativeParkour Bukkit plugin!";
require_once '../includes/haut-html.php';
documentation ();
?>
<section class="texte">
	<h1>Adding a parkour map to this website</h1>
	<p>
		Everyone can add and share parkour maps on this website. It is easy to
		do and it requires a server running the CreativeParkour Bukkit plugin
		(<a href="doc/play.php">help</a>).
	</p>

	<h2>Enabling map sharing (for server admins)</h2>
	<p>
		If you are an admin on your server, you must enable parkour map
		sharing to allow players to share their maps. To do it, type <span
			class="commande">/cp config sharing</span>, click <em>YES</em> and
		follow the instructions to register your server on <em>creativeparkour.net</em>.<br />
		After that, players are allowed to share their maps. You can use the <span
			class="commande">creativeparkour.share</span> permission to choose
		who is able to do it.
	</p>

	<h2>Adding a map to the website</h2>
	<p>
		You can share parkour maps you created with CreativeParkour on this
		website and add them in the <a href="maplist.php">map list</a> by
		using the <span class="commande">/cp share</span> command (you have to
		be in a map you created, type <span class="commande">/cp play</span>
		and find one). If you are allowed to share your map, you will be asked
		by the plugin to click a link in the Minecraft chat. This link
		redirects you to the website to confirm your map sharing, <strong>don't
			forget to do it or your map will never be added to the list</strong>.
	</p>
</section>
<?php
documentation ( true, true );
include_once '../includes/bas.php';
?>