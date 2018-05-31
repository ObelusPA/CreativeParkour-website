<?php
require_once '../includes/haut-global.php';
$titre = "Map creation tutorial";
$suffixeTitre = "documentation";
$metaDescription = "The CreativeParkour Bukkit plugin provides a secured map builder to allow everyone to easily create fun parkour maps with features like potion effects or WorldEdit wand.";
$canonical = "https://creativeparkour.net/doc/map-creation.php";
require_once '../includes/haut-html.php';
documentation ();
?>
<section class="texte">
	<h1>Map creation</h1>
	<p>
		To play parkour maps, they have to be created before! The
		CreativeParkour Bukkit plugin provides a secured map builder to allow
		everyone to easily create fun parkour maps.<br /> This tutorial will
		teach you how to use this feature in Minecraft.
	</p>

	<h2>Entering the map builder</h2>
	<p>
	<?php imageFlottanteDoc("images/doc/choix-creation.png", 300, "images/doc/opti/choix-creation.png");?>
		To start creating a map, use the <span class="commande">/cp create</span>
		command and click <em>Create a new map</em>.<br /> If you leave the
		map creation with <span class="commande">/cp leave</span>, you will
		have to click <em>Load last edited map</em> to continue building your
		map.<br /> You can work only on one map at time (except if you are
		invited by another player), creating a new map will delete your last
		edited map if you have not published it.<br /> <br /> You have a cubic
		area to build your map. Its size has been defined by a server admin in
		<em>configuration.yml</em> (64 by default). An admin can change this
		size at any time, it will only affect new maps.
	</p>

	<h2>Building the parkour</h2>
	<p>When you create a map, you are in creative mode. You can use all the
		Minecraft blocks, but there are a few limitations to prevent grief:
		you can not open blocks' inventories (chests, dispensers...), you
		can't spawn entities like mobs or minecarts and you can't use
		dangerous blocks like TNT or mob spawners. Server admins can also
		disable redstone and liquids.</p>

	<h3>Inviting other players</h3>
	<p>
		You can invite your friends to build your parkour with you by using
		the following command: <span class="commande">/cp invite
			&#60;player&#62;</span> (replace <span class="commande">&#60;player&#62;</span>
		by your friend's name). They will receive an invitation and they will
		automatically be teleported to your map if they accept it. If they
		want to come back to your map after they leave, they will have to use
		the <em>Other maps</em> item after typing <span class="commande">/cp
			create</span>.<br /> To disallow someone to build your map with you,
		use <span class="commande">/cp remove &#60;player&#62;</span>.<br />You
		can also use <span class="commande">/cp contributors</span> to see the
		list of players you invited and easily remove them.
	</p>

	<h3>Special signs</h3>
	<p>
		In CreativeParkour, map creators have to place special signs to define
		map spawn, checkpoints, endind and more.<br /> These signs can be
		placed on any block you want, there is no special block in
		CreativeParkour. Special signs disappear when you publish your map
		(but they still work), so make sure that players step on these blocks
		during their course or use something to indicate them.
	</p>

	<h4 id="spawn sign">Spawn sign (required)</h4>
	<p>
		<?php imgPanneau(["<spawn>"], 170);?>
		This sign must be placed where you want players to spawn when they come to play your map. To create it, just write <span
			class="commande">&#60;spawn&#62;</span> on its first line. This is
		how special signs work.
	</p>

	<h4>Start sign (required)</h4>
	<p>
		<?php imgPanneau(["<start>"], 170);?>
		Place the <span class="commande">&#60;start&#62;</span> sign where you
		want players' timer to start. Make sure that players pass it or their
		timer will never start.<br /> You can place several <span
			class="commande">&#60;start&#62;</span> signs, they will start
		players' timer only once (until players reset it).
	</p>

	<h4>Checkpoint signs</h4>
	<p>
		<?php imgPanneau(["<checkpoint>", "optional"], 170);?>
		<?php imgPanneau(["<checkpoint>"], 170);?>
		You may have to place checkpoints in your parkour to make it easier and to allow players to return to them when they fall. To do this, place <span
			class="commande">&#60;checkpoint&#62;</span> signs. Please note that
		players have to pass all the checkpoints before reaching the end of
		your parkour, if they miss one, they will not be able to finish your
		map. But you can make optional checkpoints by writing <span
			class="commande">optional</span> (or just <span class="commande">o</span>)
		on the second line of a checkpoint sign.
	</p>

	<h4>End sign (required)</h4>
	<p>
		<?php imgPanneau(["<end>"], 170);?>
		To finish your parkour map, place the <span class="commande">&#60;end&#62;</span>
		sign where you want player's timer to stop.<br /> You can place
		several <span class="commande">&#60;end&#62;</span> signs to make a
		bigger ending area for example.
	</p>

	<h4>Death sign</h4>
	<p>
		<?php imgPanneau(["<death>"], 170);?>
		You can place a <span class="commande">&#60;death&#62;</span> sign in
		your map. Its Y position defines the height at which players are
		teleported back to the last checkpoint (or to the start point).<br />
		If you use this sign, players no longer have to use their items to
		respawn when they fall.
	</p>

	<h4>Death block signs</h4>
	<p>
		<?php imgPanneau(["<deathb>"], 170);?>
		<span class="commande">&#60;deathb&#62;</span> signs can be placed
		where you want players that step on them to be instantly killed and
		teleported back to last checkpoint they passed (or to start). It is a
		good idea to indicate death blocks to players by placing different
		blocks under them.<br /> <strong>Tip:</strong> WorldEdit can be used
		to fill large areas with death blocks signs, use <span
			class="commande">//set sign|&#60;deathb&#62;</span>. It also works
		with other signs and WorldEdit commands, more info is on <a
			href="http://wiki.sk89q.com/wiki/WorldEdit/Block_data_syntax#Sign_text"
			target="_blank">WorldEdit's wiki</a>.
	</p>

	<h4 id="effect signs">Effect signs</h4>
	<p>
		<?php imgPanneau(["<effect>", "JUMP_BOOST", "10", "1"], 170);?>
		Potion effects can be used to make parkour maps more fun! Available effects are <em>speed</em>,
		<em>slowness</em> (or <em>slow</em>), <em>jump</em> (or <em>jump_boost</em>),
		<em>nausea</em> (or <em>confusion</em>), <em>blindness</em>, <em>night_vision</em>
		and <em>levitation</em>.<br /> To make an effect sign, write <span
			class="commande">&#60;effect&#62;</span> on its first line, the name
		of the effect on the second line, the effect duration in seconds on
		the third line and the effect amplifier on the last line. It works
		like Minecraft's <span class="commande">/effect</span> command, the
		player loses the effect if you set the duration to 0 and you have to
		set the amplifier to 0 to make a level 1 effect. For example, the sign
		on the right will give to players who step on the block the jump boost
		effect level 2 during 10 seconds.<br />A 9999 seconds (or more)
		duration makes your effect infinite.<br /> Players do not lose their
		effect when they return to checkpoints, but that is the case when they
		return to start. If you want them to lose their effect when returning
		to checkpoints, place effect signs with duration 0 after the
		checkpoints.
	</p>

	<h4 id="give">Giving ender pearls, Elytra and firework rockets</h4>
	<p>
		<?php imgPanneau(["<give>", "elytra", "take"], 170);?>
		<?php imgPanneau(["<give>", "elytra"], 170);?>
		<?php imgPanneau(["<give>", "ender pearl", "take"], 170, true);?>
		<?php imgPanneau(["<give>", "ender pearl"], 170);?>
		<?php imgPanneau(["<give>", "firework", "take"], 170, true);?>
		<?php imgPanneau(["<give>", "firework"], 170);?>
		Parkour can be more than jumping, you can use <span class="commande">&#60;give&#62;</span>
		signs to give players ender pearls, <a
			href="http://minecraft.gamepedia.com/Elytra" target="_blank">Elytra</a>,
		and/or firework rockets (that can be used as a boost when flying with
		Elytra). Ender pearl and firework rocket signs give infinite items
		until players step on a sign that removes them.<br /> There are two
		types of <span class="commande">&#60;give&#62;</span> signs for each
		item: signs to give the item and signs to remove it from player's
		inventory. As you can see on the right, write the item name on the
		second line of the sign and <span class="commande">give</span> (or
		nothing) or <span class="commande">take</span> on the third line to
		choose if players receive or lose the item when they step on the sign.<br />
		Players automatically lose these items when they return to start in
		your map, but not when they return to the last checkpoint. Make sure
		that they cannot cheat your parkour!<br /> If you want to make an
		Elytra course but you don't have enough space, <a
			href="doc/faq.php#bigger maps">click here</a> for information about
		making maps bigger.
	</p>

	<h4 id="tp">Telporting players</h4>
	<p>
		<?php imgPanneau(["<tp>", "30", "14", "50.5"], 170);?>
		You can place signs to teleport players to specific coordinates in your map. To do it, use <span
			class="commande">&#60;tp&#62;</span> signs and write X, Y and Z
		coordinates of where you want to teleport players on lines 2, 3 and 4
		of the sign. For example, the sign of the right will teleport players
		that step on it to X=30, Y=14 and Z=50.5.<br /> Like with <span
			class="commande">/tp</span> command, you have to add 0.5 to X and Z
		coordinates of a block to teleport players at the center of it.
	</p>

	<h3 id="WorldEdit">WorldEdit</h3>
	<p>
		To help you building your parkour map faster, WorldEdit can securely
		be used in the parkour builder. The <a target="_blank"
			href="http://dev.bukkit.org/bukkit-plugins/worldedit/">WorldEdit</a>
		plugin must be installed on your server if you want to use this
		feature.<br /> You can use the given wooden axe like the usual
		WorldEdit wand: select two positions (the first with left-click, the
		second with right-click), and then use WorldEdit commands to place
		blocks. For example, use <span class="commande">//set &#60;block&#62;</span>
		to fill the cubic area defined by the two blocks you clicked on with
		the block you want.<br /> Available WorldEdit commands are <span
			class="commande">//wand</span>, <span class="commande">//toggleeditwand</span>,
		<span class="commande">//pos1</span>, <span class="commande">//pos2</span>,
		<span class="commande">//set</span>, <span class="commande">//undo</span>,
		<span class="commande">//redo</span>, <span class="commande">//clear</span>,
		<span class="commande">//replace</span>, <span class="commande">//copy</span>,
		<span class="commande">//cut</span>, <span class="commande">//paste</span>,
		<span class="commande">//hollow</span>, <span class="commande">//center</span>,
		<span class="commande">//naturalize</span>, <span class="commande">//walls</span>,
		<span class="commande">//faces</span>, <span class="commande">//smooth</span>,
		<span class="commande">//count</span>, <span class="commande">//cyl</span>,
		<span class="commande">//hcyl</span>, <span class="commande">//sphere</span>,
		<span class="commande">//hsphere</span>, <span class="commande">//pyramid</span>,
		<span class="commande">//hpyramid</span>, <span class="commande">//flip</span>,
		<span class="commande">//rotate</span>, <span class="commande">//stack</span>.
		<a href="http://wiki.sk89q.com/wiki/WorldEdit/Reference"
			target="_blank">Click here</a> to see WorldEdit's documentation about
		these commands. <br />You can find all the block IDs on websites like
		<a rel="nofollow" target="_blank"
			href="http://minecraft-ids.grahamedgecombe.com/">this one</a>.<br />
		If you do something wrong, you can use the <span class="commande">//undo</span>
		command to revert the last thing you done with WorldEdit, and there is
		<span class="commande">//redo</span> to redo the last undone action.<br />
		<br /> <em>Server admins: you can install the <a
			href="https://www.spigotmc.org/resources/asyncworldedit.327/"
			target="_blank">AsyncWorldEdit plugin</a> to avoid lag when players
			use WorldEdit in CreativeParkour.
		</em>
	</p>

	<h3 id="special options">Special options</h3>
	<p>
		You can enable special options to make your parkour original with <span
			class="commande">/cp mapoptions</span>.<br /> Available options:<br />
		&nbsp;&bull; No sneak: disables sneak in the map<br /> &nbsp;&bull;
		Deadly lava: kills players when they touch lava (this can be useful to
		make deadly areas without using signs)<br /> &nbsp;&bull; Deadly
		water: kills players when they touch water<br /> &nbsp;&bull; No
		interactions: prohibits players to interact with doors, trapdoors and
		fence gates.<br />
	</p>

	<h2>Testing your map</h2>
	<p>
		<?php imageFlottanteDoc("images/doc/map-status.png", 200, "images/doc/opti/map-status.png"); ?>
		Before publishing your parkour map, you have to test it to be sure
		that it is possible to reach the end point. To test your map, type <span
			class="commande">/cp test</span>. You can do it as many times as you
		want. When you reach the end of your map, your map status changes from
		<em>unvalidated</em> to <em>validated</em>, it significates that you
		are able to publish your map. Your map will remain validated until you
		place or break a block.<br /> You can leave the test mode at any time
		and continue building your map by typing <span class="commande">/cp
			test leave</span>.
	</p>

	<h2>Publishing your map</h2>
	<p>
		You can now publish your map and let other players play it! Type <span
			class="commande">/cp publish &#60;name&#62;</span> (replace <span
			class="commande">&#60;name&#62;</span> by the name you want to give
		to your map, you can use spaces).
	</p>

	<h2 id="sharing-your-map">Sharing your map</h2>
	<p>
		<?php imageFlottanteDoc("images/doc/map-options.png", 400, "images/doc/opti/map-options.png"); ?>
		You can share your map with the CreativeParkour community and show
		your nice parkour to all the world! To do it, type <span
			class="commande">/cp share</span> while being in your map or use the
		crafting table item.<br /> After you have confirmed your sharing, your
		map will be displayed in the <a href="maplist.php">map list</a> on
		this website.
	</p>

	<h2>Editing your map</h2>
	<p>
		If you want to change something in your map, type <span
			class="commande">/cp edit</span> or click <em>Edit this map</em> in
		the crafting table item. Your map will be back in creation mode until
		you publish it again.<br /> <strong>Warning: if you break or place a
			block, your map's leaderboard will be deleted and all players' times
			will be lost.</strong><br /> If you shared your map before editing
		it, you will have to share it again to replace your old map.
	</p>

	<h2>Deleting your map</h2>
	<p>
		You can delete your map by typing <span class="commande">/cp delete</span>
		or by using the <em>Map options</em> item. <strong>This cannot be
			undone!</strong><br /> If you shared your map, it will not be
		automatically deleted from the website, you have to do it yourself
		(find your map <a rel="nofollow" href="user">here</a>).
	</p>
</section>
<?php
documentation ( true, true );
include_once '../includes/bas.php';
?>