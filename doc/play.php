<?php
require_once '../includes/haut-global.php';

$titre = "How to play?";
$suffixeTitre = "documentation";
$metaDescription = "Join a Minecraft server or install the CreativeParkour Bukkit/Spigot plugin on your server to create and play fun parkour maps!";
require_once '../includes/haut-html.php';
documentation ();
?>
<section class="texte">
	<h1>How to play CreativeParkour?</h1>
	<p>
		CreativeParkour is a Bukkit plugin, so it has to be installed on a
		Minecraft server (Spigot) to play, build, download and share parkour
		maps.<br /> If you know a server that has CreativeParkour, you can
		find maps created by the community by typing in Minecraft the command
		<span class="commande">/cp&nbsp;play</span>. You can also browse the <a
			href="maplist.php">map list</a> and type <span class="commande">/cp&nbsp;download
			&lt;number&gt;</span> or <span class="commande">/cpd &lt;number&gt;</span>
		(replace <span class="commande">&lt;number&gt;</span> by the number
		given for the map you want to download in the map list).<br /> <br />
		You can also install CreativeParkour on you own Spigot server. If you
		don't know how to create a Spigot server, there are plenty of
		tutorials on the Internet to help you.<br /> To install
		CreativeParkour, <a href="download.php" target="_blank">download it</a>
		and paste the JAR file in your <em>plugins</em> folder.
	</p>
	<div class="barre">
		<a href="download.php" target="_blank">Download CreativeParkour.jar</a>
	</div>
	<p>
		When the <em>CreativeParkour.jar</em> file is in the <em>plugins</em>
		folder, start your server and log in as an operator, you will be
		guided to configure and enable the plugin.<br /> <br /> After that,
		the plugin is ready for use, you can have start building parkour maps
		by typing <span class="commande">/cp&nbsp;create</span> or play maps
		that have been created by the community by typing <span
			class="commande">/cp&nbsp;play</span>.<br /> If you want, you can
		also directly browse maps on this website by clicking <a
			href="maplist.php">here</a>.
	</p>

	<div class="avertissement">
		For full plugin documentation, <a href="doc">click here</a>.
	</div>

	<p>
		If you need help, leave a message on the CreativeParkour's <a
			target="_blank"
			href="http://dev.bukkit.org/bukkit-plugins/creativeparkour/">Bukkit</a>
		or <a target="_blank"
			href="https://www.spigotmc.org/resources/creativeparkour.17303/">Spigot</a>
		page or <a href="contact.php">send a private message to the plugin
			author here</a>.
	</p>


</section>
<?php
documentation ( true );
include_once '../includes/bas.php';
?>