<?php
require_once '../includes/haut-global.php';
$titre = "FAQ";
$suffixeTitre = "documentation";
$metaDescription = "Frequently asked questions about the CreativeParkour Bukkit plugin.";
require_once '../includes/haut-html.php';
documentation ();
?>
<section class="texte">
	<h1>FAQ</h1>
	<p>
		On this page, you can find several frequently asked questions to help
		you using the CreativeParkour Bukkit plugin and better understand how
		it works.<br /> If you don't find what you want here, search the <a
			href="/doc">documentation</a> or <a href="contact.php">contact the
			author</a>.
	</p>
	<hr />

	<h2>How to rename a parkour map?</h2>
	<p>
		Go in the map, type <span class="commande">/cp edit</span> (or right
		click the crafting table in your inventory), and publish your map with
		<span class="commande">/cp publish &#60;new name&#62;</span>. Make
		sure to not break or place any block in the map or leaderboard will be
		wiped.
	</p>
	<hr />

	<h2 id="bigger maps">How to make parkour maps bigger?</h2>
	<p>
		You cannot make existing maps bigger, but you can change the default
		size of new maps by changing "map size" in <a
			href="doc/configuration.php">configuration.yml</a>.<br />To start
		building a new map with the size you set, type <span class="commande">/cp
			create</span> and click "Create a new map".
	</p>
	<hr />

	<h2>How to hide or reduce checkpoint messages?</h2>
	<p>
		Use <span class="commande">/cp messages</span> and click your favorite
		checkpoint validation message type (full, reduced or none).
	</p>
	<hr />

	<h2>How to fix WorldEdit permission problems ("You are not permitted to
		do that. Are you in the right mode?")</h2>
	<p>
		Install the <a href="https://dev.bukkit.org/bukkit-plugins/vault/"
			target="_blank">Vault</a> Bukkit plugin on your server,
		CreativeParkour uses it to fix WorldEdit permission issues.
	</p>
	<hr />

	<h2>How to prohibit players to create parkour maps?</h2>
	<p>
		Give them the opposite permission of <span class="commande"
			style="white-space: nowrap;">creativeparkour.create</span>. This is
		explained <a href="doc/permissions#warn">here</a>.
	</p>
	<hr />

	<h2>Is CreativeParkour compatible with Multiverse?</h2>
	<p>CreativeParkour does not use Multiverse to manage worlds, but there
		is no problem in having CreativeParkour and Multiverse running on the
		same server.</p>
	<hr />

	<h2>When do player ghosts are uploaded to the website and appear in
		leaderboards?</h2>
	<p>Player ghosts are regulary sent by your server to the website. After
		that, they are checked before showing on the website to remove
		cheaters.</p>
	<hr />

	<h2>When does my server appear on the website?</h2>
	<p>
		After registering your server with <span class="commande">/cp config
			sharing</span>, at least one parkour map has to be shared by a player
		and your server will appear on the website at the same time as the
		map.
	</p>
	<hr />

	<h2>Who are all these unknown players in my leaderboards?</h2>
	<p>
		In downloaded parkour maps, the leaderboard and ghosts are
		synchronized with this website and all the servers around the world
		that use the plugin. If you only want your players in leaderboards,
		set <span class="commande">online.download ghosts</span> to <span
			class="commande">false</span> in <a href="doc/configuration.php">configuration.yml</a>.
	</p>
	<hr />

	<h2>Why does it say "Unknown map" on map items?</h2>
	<p>
		You enabled advanced tooltips in Minecraft. Use <span class="commande">F3</span>+<span
			class="commande">H</span> to disable this.
	</p>
	<hr />

	<h2>Do you want to build a snowman?</h2>
	<p>Come on, let's go and play.</p>
	<hr />
	<br />

</section>
<?php
documentation ( true );
include_once '../includes/bas.php';
?>