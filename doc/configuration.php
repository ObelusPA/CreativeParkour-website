<?php
require_once '../includes/haut-global.php';
$titre = "Plugin configuration";
$suffixeTitre = "documentation";
$metaDescription = "List of the configuration options of the the CreativeParkour Bukkit plugin.";
$canonical = "https://creativeparkour.net/doc/configuration.php";
require_once '../includes/haut-html.php';
documentation();
?>
<section class="texte">
	<h1>Plugin configuration</h1>
	<p>
		The CreativeParkour Bukkit plugin uses several YAML files to store
		data and configuration. They are created in the <em>&#60;your&nbsp;server&#62;/plugins/CreativeParkour</em>
		folder.
	</p>
	<h2>configuration.yml</h2>
	<table class="tableau">
		<thead>
			<tr>
				<th>Item</th>
				<th>Type</th>
				<th>Default&nbsp;value</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>plugin enabled</td>
				<td>Boolean</td>
				<td>true</td>
				<td>Specifies if the plugin is enabled or not.</td>
			</tr>
			<tr>
				<td>enable auto updater</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, the plugin will automatically updated on server start
					if there is an update.</td>
			</tr>
			<tr>
				<td>enable data collection</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, the plugin will send stats and report errors.</td>
			</tr>
			<tr>
				<td>language</td>
				<td>String</td>
				<td>enUS</td>
				<td>Plugin's language. Several translations are available, and
					everyone can contribute! <a href="doc/languages.php">Click here for
						more info</a>.
				</td>
			</tr>
			<tr id="prefix">
				<td>prefix</td>
				<td>String</td>
				<td>§e[§6CreativeParkour§e]</td>
				<td>Prefix displayed in chat before all CreativeParkour messages.
					You can use Minecraft <a
					href=https://minecraft.gamepedia.com/Formatting_codes
					" target="_blank">formatting codes</a> to customize it.<br /> The
					default prefix is <span class="jauneMC">[</span><span
					class="goldMC">CreativeParkour</span><span class="jauneMC">]</span>.
				</td>
			</tr>
			<tr>
				<td>sign brackets</td>
				<td>String</td>
				<td>triangle</td>
				<td>To change brackets around sign tags (like <span class="commande">&#60;start&#62;</span>).
					Possible values are <span class="commande">triangle</span> for
					&#60;&#62;, <span class="commande">square</span> for [] and <span
					class="commande">round</span> for ().<br /> <strong>Remember that
						all the documentation uses triangle brackets, so if you change
						this parameter, make sure to inform your players.</strong>
				</td>
			</tr>
			<tr>
				<td>debug</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If true, debug messages will be displayed in console.</td>
			</tr>
			<tr id="memory dump interval">
				<td>memory dump interval</td>
				<td>Integer</td>
				<td>20</td>
				<td>Minutes between two memory dumps. Set to <span class="commande">0</span>
					to disable.<br />This deletes useless elements from memory, and it
					can also improve general performance.
				</td>
			</tr>
			<tr id="dont use cp">
				<td>dont use cp</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If true, <span class="commande">/cp</span> command is disabled
					and <span class="commande">/cpk</span> must be used instead. This
					option is useful if you have another plugin that uses the <span
					class="commande">/cp</span> command on your server.<br /> <strong>Keep
						in mind that all the documentation and all in-game messages use <span
						class="commande">/cp</span>, you must clearly tell your players to
						use <span class="commande">/cpk</span> instead or they will not be
						able to use CreativeParkour.
				</strong></td>
			</tr>
			<tr>
				<td>installation date</td>
				<td>Timestamp</td>
				<td>Current timestamp</td>
				<td>Date at which the plugin started for the first time, shall not
					be modified.</td>
			</tr>
			<tr>
				<td>languages version</td>
				<td>String</td>
				<td>Current version of the plugin</td>
				<td>Last languages update version, shall not be modified.</td>
			</tr>
			<tr id="map storage">
				<td>map storage.map storage world</td>
				<td>World name</td>
				<td>CreativeParkourMaps</td>
				<td>The name of the world where parkour maps are stored.</td>
			</tr>
			<tr>
				<td>map storage.use plugin world</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, the plugin will create a world called <em>CreativeParkourMaps</em>
					and maps will be stored in. <strong>It is highly recommended to use
						this world to prevent bugs.</strong></td>
			</tr>
			<tr>
				<td>map storage.storage location x min</td>
				<td>Integer</td>
				<td>0</td>
				<td rowspan="3">Location from which the plugin will generate parkour
					maps (<strong>maps can be created at any greater coordinates than
						this location</strong>).
				</td>
			</tr>
			<tr>
				<td>map storage.storage location y min</td>
				<td>Integer</td>
				<td>10</td>
			</tr>
			<tr>
				<td>map storage.storage location z min</td>
				<td>Integer</td>
				<td>0</td>
			</tr>
			<tr>
				<td>map storage.map size</td>
				<td>Integer</td>
				<td>64</td>
				<td>Maps' edge. If this size is 64, maps will have a size of
					64×64×64.<br />This does not affect already published maps, you can
					change the value when you want without breaking everything.
				</td>
			</tr>
			<tr>
				<td>map storage.gap</td>
				<td>Integer</td>
				<td>30</td>
				<td>Size of the empty space between parkour maps.</td>
			</tr>
			<tr>
				<td>map creation.allow redstone</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If false, redstone will be disabled in maps.</td>
			</tr>
			<tr>
				<td>map creation.allow fluids</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If false, water and lava will be disabled in maps. Note that
					players cannot drown or be hurt by lava in parkour maps.</td>
			</tr>
			<tr>
				<td>map creation.disable potion effects</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If true, <a href="doc/map-creation.php#effect signs"><em>&#60;effect&#62;</em>
						signs</a> will be disabled.
				</td>
			</tr>
			<tr>
				<td>map creation.announce new maps</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, a message will be sent to all players on the server
					when a new map is published.</td>
			</tr>
			<tr id="map creation.maps per player limit">
				<td>map creation.maps per player limit</td>
				<td>Integer</td>
				<td>1000</td>
				<td>Maximum number of maps that players who do not have the <a
					href="doc/permissions.php#creativeparkour.infinite">"creativeparkour.infinite"
						permission</a> can create.
				</td>
			</tr>
			<tr>
				<td>map creation.worldedit item</td>
				<td>Item name</td>
				<td>WOOD_AXE</td>
				<td>Name of the item used as WorldEdit's wand on the server. This
					should not be changed unless you do not use the default wooden axe
					wand.<br /> <strong>An exact item name from <a
						href="https://hub.spigotmc.org/javadocs/bukkit/org/bukkit/Material.html"
						target="_blank">this list</a> must be used.
				</strong>
				</td>
			</tr>
			<tr>
				<td>map selection.display records</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, player records will be displayed in map selection GUI (<span
					class="commande">/cp play</span>). Setting this option to false
					will improve performance and reduce lag when a player uses this
					command, it can be useful if you have many maps and players.
				</td>
			</tr>
			<tr>
				<td>game.max players per map</td>
				<td>Integer</td>
				<td>-1</td>
				<td>Maximum number of players that can be in a parkour map. Put <span
					class="cmd">-1</span> to disable this.
				</td>
			</tr>
			<tr>
				<td>game.max players in storage world</td>
				<td>Integer</td>
				<td>-1</td>
				<td>Maximum number of players that can be in the world where the
					plugins stores maps. Put <span class="cmd">-1</span> to disable
					this.
				</td>
			</tr>
			<tr>
				<td>game.save inventory</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, the plugin will store players' inventories when they
					will play or create maps and give them back when they leave.<br />If
					you have a PerWorldInventory-like plugin, it is recommended to set
					this to false and configure a new inventory group for
					CreativeParkourMaps world.
				</td>
			</tr>
			<tr id="game.force empty inventory">
				<td>game.force empty inventory</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If true, players must empty their inventory before playing or
					creating a map (consequently, it sets "game.save inventory" to
					false).</td>
			</tr>
			<tr>
				<td>game.inventory recovery world exclusions</td>
				<td>World name list</td>
				<td>- example_world_1<br />- example_world_2
				</td>
				<td>List of world names. If a player teleports to one of these
					worlds when leaving CreativeParkour, they will not recover the
					inventory that has been saved when they joined.This can be used to
					prevent players to recover their survival inventory when they
					directly teleport to the creative world for example.</td>
			</tr>
			<tr id="game.exit location.world">
				<td>game.exit location.world</td>
				<td>World name</td>
				<td rowspan="6"><em>Default world's spawn location</em></td>
				<td rowspan="6">Location where players are teleported if you set
					"game.always teleport to exit location" (below) to <em>true</em> or
					if their previous location cannot be recovered (it should never
					happen).
				</td>
			</tr>
			<tr>
				<td>game.exit location.x</td>
				<td>Double</td>
			</tr>
			<tr>
				<td>game.exit location.y</td>
				<td>Double</td>
			</tr>
			<tr>
				<td>game.exit location.z</td>
				<td>Double</td>
			</tr>
			<tr>
				<td>game.exit location.pitch</td>
				<td>Float</td>
			</tr>
			<tr>
				<td>game.exit location.yaw</td>
				<td>Float</td>
			</tr>
			<tr>
				<td>game.always teleport to exit location</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If true, players will be always teleported to the location
					specified above instead of where they were when joining a parkour
					map.</td>
			</tr>
			<tr>
				<td>game.exit on login</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If true, players that login in maps are teleported where they
					were before joining CreativeParkour instead of staying in the map.</td>
			</tr>
			<tr id="game.update players before teleporting">
				<td>game.update players before teleporting</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If false, inventories are given back to players <strong>after</strong>
					changing world when leaving maps (also for game mode, health...).<br />
					If true, inventories are given back to players <strong>before</strong>
					changing world, so other plugins are free to change inventories
					(and change game mode) when players teleport between worlds. This
					second option should only be used if players do not join
					CreativeParkour with an important inventory, or other plugins may
					delete it on world change.
				</td>
			</tr>
			<tr id="game.only leave with creativeparkour command">
				<td>game.only leave with creativeparkour command</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If true, players will only be allowed to use <span
					class="commande">/cp leave</span> to leave CreativeParkour (so they
					will not be able to use commands like <span class="commande">/spawn</span>
					to leave). This option can be used to prevent issues with worlds,
					inventories, gamemodes...
				</td>
			</tr>
			<tr>
				<td>game.negative leaderboard</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If true, leaderboard's times are negative so that the fastest
					time would be on top.</td>
			</tr>
			<tr id="game.disable leaderboards">
				<td>game.disable leaderboards</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If true, there will not be leaderboards in CreativeParkour,
					players' times will not be saved, and ghosts will be disabled. The
					main purpose of this option is to improve performance by removing
					the time spent loading leaderboards.</td>
			</tr>
			<tr>
				<td>game.enable map rating</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, players will be able to rate maps difficulties when
					they reach the end.</td>
			</tr>
			<tr>
				<td>game.freeze redstone</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If true, redstone will be automatically frozen in maps where no
					one is.</td>
			</tr>
			<tr id="game.pressure plates as special blocks">
				<td>game.pressure plates as special blocks</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If true, pressure plates will be used to detect players on
					special blocks instead of constantly checking their location when
					they move. This option improves performance when there are many
					players. It can be disabled in some specific maps using the <a
					class="cmd" href="doc/commands.php#noplates">/cp noplates</a>
					command.
				</td>
			</tr>
			<tr id="game.milliseconds difference">
				<td>game.milliseconds difference</td>
				<td>Integer</td>
				<td>10000</td>
				<td>Number of milliseconds per minute that are tolerated between the
					time in ticks and the real time that a players takes to complete a
					parkour. If the average difference for each minute is higher than
					the value you set, the time will not be saved. This can be useful
					to prevent the plugin from saving incorrect times when the server
					is laggy (with a bad TPS). Set to 0 to disable.</td>
			</tr>
			<tr>
				<td>game.enable ghosts</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, player ghosts will be enabled in CreativeParkour.
					Players will be able to select ghosts they want to run parkours
					against. You can choose who is allowed to see and create ghosts
					using <a href="doc/permissions.php">permissions</a>.
				</td>
			</tr>
			<tr>
				<td>game.max ghosts</td>
				<td>Integer</td>
				<td>15</td>
				<td>Maximum number of ghosts each player can select and see in a
					parkour map.</td>
			</tr>
			<tr>
				<td>game.fetch ghosts skins</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, your server will ask creativeparkour.net ghosts' skins.
					This is disabled if "enable data collection" is false.</td>
			</tr>
			<tr>
				<td>game.sharing info in downloaded maps</td>
				<td>Boolean</td>
				<td>true</td>
				<td>Set to false to disable the message about map sharing when
					players complete downloaded courses.</td>
			</tr>
			<tr>
				<td>online.enabled</td>
				<td>Boolean</td>
				<td>false</td>
				<td>If true, the plugin will be able to communicate with this
					website to allow players to download and share maps. <a
					href="http://creativeparkour.net/doc/permissions.php">Permissions</a>
					can be used to choose who is able to do it.
				</td>
			</tr>
			<tr>
				<td>online.server uuid</td>
				<td>UUID</td>
				<td>Random</td>
				<td>The server UUID, randomly generated. Must not be changed.</td>
			</tr>
			<tr>
				<td>online.show downloadable maps</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, maps that can be downloaded from this website will be
					displayed in the map list.</td>
			</tr>
			<tr>
				<td>online.upload ghosts</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, player ghosts will regularly be uploaded to this
					website to share them with the community.</td>
			</tr>
			<tr>
				<td>online.download ghosts</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, ghosts will be downloaded from the website when players
					select them (only in downloaded or shared maps).</td>
			</tr>
			<tr id="rewards.currency">
				<td>rewards.currency</td>
				<td>String</td>
				<td>MONEY × %amount</td>
				<td>The way you want CreativeParkour to display money amounts in <a
					href="doc/rewards.php">rewards</a>. The string must contain <span
					class="commande">%amount</span> thay will be displayed as a number.
					For example, you can set <span class="commande">$%amount</span> to
					display <em>$100</em> to players that get 100 dollar rewards in
					CreativeParkour.
				</td>
			</tr>
			<tr id="rewards.claim worlds all">
				<td>rewards.claim worlds all</td>
				<td>Boolean</td>
				<td>true</td>
				<td>If true, players are allowed to claim rewards in all the worlds
					(setting this to true will make the "claim worlds" list below
					useless).</td>
			</tr>
			<tr id="rewards.claim worlds">
				<td>rewards.claim worlds</td>
				<td>World name list</td>
				<td>- world<br />- world_nether<br />- world_the_end<br /> <em>And
						the other worlds...</em></td>
				<td>Players can claim their <a href="doc/rewards.php">rewards</a>
					only while being in one of the world of this list. For example,
					only set your survival world to force players to get rewards only
					in this world, and nowhere else.<br /> <strong>This list is useless
						if "claim worlds all" option above is set to true.</strong>
				</td>
			</tr>
		</tbody>
	</table>
	<h2>Map files</h2>
	<p>
		Data about parkour maps is stored in the <em>Maps</em> folder. It
		contains one file for each map, files' names are map ids that can be
		found in Minecraft with <span class="commande">/cp managemaps</span>
		or <span class="commande">/cp getid</span>.<br />These files should
		not be edited or maps can be corrupted.
	</p>
	<h2>Time files</h2>
	<p>
		Players' times and ghosts in different parkours are stored in files in
		the <em>Times</em> folder.<br />Files are named like this: <em>&#60;map
			UUID&#62;_&#60;player UUID&#62;.yml</em><br /> Deleting a time file
		will delete the player's time and ghost in the related parkour map.
	</p>
	<h2>Player files</h2>
	<p>
		Player data stored in files in the <em>Players</em> folder.<br />Files'
		name are players' UUID. They contain player preferences, inventory,
		etc.
	</p>
	<h2>lobby signs.yml</h2>
	<p>
		Contains data about <a href="doc/lobby-signs.php">signs used to join
			maps or display leaderboards</a>.
	</p>
	<h2>rewards.yml</h2>
	<p>
		This file can be used to create <a href="doc/rewards.php">custom
			rewards</a>.
	</p>
	<h2>WorldEdit configuration</h2>
	<p>Creativearkour uses WorldEdit in creation mode. All WorldEdit
		settings apply in CreativeParkour.</p>
</section>
<br />
<?php
documentation(true);
include_once '../includes/bas.php';
?>