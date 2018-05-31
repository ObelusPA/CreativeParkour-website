<?php
require_once '../includes/haut-global.php';
$titre = "Documentation";
$metaDescription = "Documentation for the CreativeParkour Bukkit plugin (how to play, how to create parkour maps...)";
$canonical = "https://creativeparkour.net/doc";
require_once '../includes/haut-html.php';
documentation ();
?>
<section class="texte">
	<h1>Plugin documentation</h1>
	<p>
		Here is the list of the documentation pages that can help you using
		the CreativeParkour Bukkit plugin:<br /> &nbsp;&bull; <a
			href="doc/map-creation.php">Parkour map creation tutorial</a><br />
		&nbsp;&bull; <a href="doc/commands.php">Commands</a><br />
		&nbsp;&bull; <a href="doc/permissions.php">Permissions</a><br />
		&nbsp;&bull; <a href="doc/configuration.php">Plugin configuration</a><br />
		&nbsp;&bull; <a href="doc/add-map.php">Adding a map to the website</a><br />
		&nbsp;&bull; <a href="doc/lobby-signs.php">Creating lobby signs to
			join parkour maps or display learderboards</a><br /> &nbsp;&bull; <a
			href="doc/rewards.php">Custom rewards</a><br /> &nbsp;&bull; <a
			href="doc/languages.php">Languages</a><br /> &nbsp;&bull; <a
			href="doc/faq.php">FAQ</a><br /> &nbsp;&bull; <a
			href="https://github.com/ObelusPA/CreativeParkour#api"
			target="_blank">API</a><br /> &nbsp;&bull; <a href="javadoc">Javadoc</a><br />
		<br /> This documentation is about the latest version of the plugin.
	</p>
</section>
<?php
include_once '../includes/bas.php';
?>