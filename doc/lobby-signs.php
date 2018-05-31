<?php
require_once '../includes/haut-global.php';
$titre = "Lobby signs";
$suffixeTitre = "documentation";
$metaDescription = "The CreativeParkour Bukkit plugin provides right-clickable signs to join and create parkour maps and display leaderboards.";
require_once '../includes/haut-html.php';
documentation();
?>
<section class="texte">
	<h1>Lobby signs</h1>
	<p>Signs can be placed anywhere on your server to allow players to
		create and play maps by clicking on them, or to display leaderboards.
		With this feature, you can for example create a parkour lobby with
		signs to join maps and walls displaying leaderboards!</p>

	<h2>"Create", "play" and "map" signs</h2>
	<p>
	<?php imageFlottanteDoc("images/doc/lobby-signs.jpg", 430);?>
		These signs are very simple to create. When a player right clicks on one of them, it works like regular commands (<span
			class="commande">/cp create</span>, <span class="commande">/cp play</span>
		and <span class="commande">/cp play &#60;map&#62;</span>). As you can
		see on the picture on the right, you have to write <span
			class="commande">&#60;cp&#62;</span> on the first line of a sign and
		its type on its second line (<span class="commande">create</span>, <span
			class="commande">play</span> or <span class="commande">map</span>).
		For <span class="commande">map</span> signs, don't forget to write on
		the third line the name of the map in which you want players to be
		teleported when they right click the sign.<br /> If the name of the
		map is too long to fit in the line, you can use its ID: find it by
		going in the map and typing <span class="commande">/cp getid</span>
		(or use <span class="commande">/cp managemaps</span>), and then write
		<span class="commande">ID:&#60;ID&#62;</span> instead of map's name on
		the sign (replace <span class="commande">&#60;ID&#62;</span> by the ID
		you found before).<br /> <br /> Data about these signs is stored in
		the <em>signs.yml</em> file. To delete a sign, just break it, it will
		be automatically removed from the file.
	</p>

	<h2>Leaderboard signs</h2>
	<p>
		You can create big leaderboard panels to display the best parkour
		players for each map! Follow these instructions to create leaderboard
		signs:<br /> <strong>Line 1:</strong> as the other signs, write <span
			class="commande">&#60;cp&#62;</span> on the first line.<br /> <strong>Line
			2:</strong> write <span class="commande">leaderboard</span> on the
		second line.<br /> <strong>Line 3:</strong> on the third line of the
		sign, write the full name of the map you want to display players'
		times (or use its ID if the name is too long, see above).<br /> <strong>Line
			4:</strong> this last line can contain two information. First, type
		the leaderboard rank of the first player you want to display the time
		on the sign. For example, write <span class="commande">1</span> for
		the best player, the sign will display their time, followed by the
		second player, followed by the third... You can also add <span
			class="commande">+name</span> after this number to tell the sign to
		display the map name on its first line.<br /> Here is an example of a
		sign leaderboard for a map called <em>Jungle Run</em>, up to the 11<sup>th</sup>
		best player. As you can see, the sign leaderboard sorts players that
		have the same number of seconds by ticks, unlike the regular map
		leaderboard.<br /> <a style="width: 100%"
			href="images/doc/lobby-signs-leaderboards.jpg" class="highslide"
			onclick="return hs.expand(this)"> <img style="width: 100%"
			src="images/doc/lobby-signs-leaderboards.jpg"
			title="Click to enlarge" /></a><br />Tip: right-clicking on a
		leaderboard sign teleports you to the parkour map.
	</p>
</section>
<?php
documentation(true);
include_once '../includes/bas.php';
?>