<?php
require_once '../includes/haut-global.php';
$titre = "Languages";
$suffixeTitre = "documentation";
$metaDescription = "The CreativeParkour Bukkit plugin can easily be translated in other languages online, on BukkitDev.";
require_once '../includes/haut-html.php';
documentation();
?>
<section class="texte">
	<h1>Languages</h1>
	<p>
		The CreativeParkour Bukkit plugin is not only in English! It uses
		online <a
			href="https://dev.bukkit.org/projects/creativeparkour/localization"
			target="_blank">BukkitDev's localization system</a> that allows
		everyone to contribute to translations. You can come to translate the
		plugin in your language, help ongoing translations, or fix mistakes,
		it will be very useful for thousands of players around the world.
		Translations are updated in each plugin update.<br />Participate on
		BukkitDev (you will need a Bukkit/Curse account, click the buttons on
		top of the page to do it):
	</p>
	<div class="barre">
		<a href="https://dev.bukkit.org/projects/creativeparkour/localization"
			target="_blank">Click here to contribute to CreativeParkour's
			translation!</a>
	</div>
	<h2>Available languages</h2>
	<p>
		&nbsp;&bull; English<br /> &nbsp;&bull; <a
			href="https://dev.bukkit.org/projects/creativeparkour/localization/languages/3/phrases"
			target="_blank">French</a> (fully translated)<br /> &nbsp;&bull; <a
			href="https://dev.bukkit.org/projects/creativeparkour/localization/languages/4/phrases"
			target="_blank">German</a> (fully translated)<br /> &nbsp;&bull; <a
			href="https://dev.bukkit.org/projects/creativeparkour/localization/languages/7/phrases"
			target="_blank">Korean</a><br /> &nbsp;&bull; <a
			href="https://dev.bukkit.org/projects/creativeparkour/localization/languages/8/phrases"
			target="_blank">Latin American Spanish</a><br /> &nbsp;&bull; <a
			href="https://dev.bukkit.org/projects/creativeparkour/localization/languages/9/phrases"
			target="_blank">Polish</a> (fully translated)<br /> &nbsp;&bull; <a
			href="https://dev.bukkit.org/projects/creativeparkour/localization/languages/10/phrases"
			target="_blank">Russian</a><br /> &nbsp;&bull; <a
			href="https://dev.bukkit.org/projects/creativeparkour/localization/languages/11/phrases"
			target="_blank">Simplified Chinese</a> (fully translated)<br />
		&nbsp;&bull; <a
			href="https://dev.bukkit.org/projects/creativeparkour/localization/languages/12/phrases"
			target="_blank">Spanish</a> (almost fully translated)
	</p>
	<h2>Additional information</h2>
	<p>
		Words beginning by "%" must not be translated, they are replaced with
		data when the plugin displays the message.<br /> When you see <span
			class="commande">%LClick here%L</span>, only translate "Click here"
		("%L" surrounds a link).<br /> Messages sometimes contain "\n", it
		represents a line break. Do not insert actual line breaks in
		translations.<br /> Help book messages contain <a
			href="http://minecraft.gamepedia.com/Formatting_codes"
			target="_blank">formatting codes</a> (§ followed by a number or a
		letter) that should not be changed.
	</p>
	<h2 id="custom">Custom language file</h2>
	<p>
		You can also create your own language file to have custom messages in
		the plugin. To do it, create a <em>txt</em> file in the <em>CreativeParkour</em>
		folder of your server. Then, write your custom messages in it. You
		must use this format: <span class="cmd"><em>&#60;phrase_name&#62;</em>=<em>&#60;message&#62;</em></span>.
		You can find phrase names in <a
			href="https://dev.bukkit.org/projects/creativeparkour/localization/languages/2/phrases"
			target="_blank">BukkitDev's localization system</a>. You can get all
		the existing messages in English or another language <a
			href="https://dev.bukkit.org/projects/creativeparkour/localization/export-minecraft-resource-string"
			target="_blank">here</a>, but you don't have to put all the messages
		in your file if you want to change only some of them.<br /> After you
		created your file, open <span class="cmd">configuration.yml</span> and
		replace the <span class="cmd">language</span> value by your file name
		(with the extension). Then, restart your server and your custom
		messages will be loaded.
	</p>
</section>
<?php
documentation(true);
include_once '../includes/bas.php';
?>