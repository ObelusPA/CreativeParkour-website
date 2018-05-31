<?php
require_once '../includes/haut-global.php';
$titre = "Commands";
$suffixeTitre = "documentation";
$metaDescription = "List of commands available in the CreativeParkour Bukkit plugin.";
$canonical = "https://creativeparkour.net/doc/commands.php";
require_once '../includes/haut-html.php';
documentation();

$commandes = array(
    array(
        "cmd" => "help",
        "aliases" => "",
        "descr" => "Displays CreativeParkour plugin's in-game help."
    ),
    array(
        "cmd" => "play [map&nbsp;name]",
        "aliases" => "join, list",
        "descr" => "Displays the parkour map list to choose a map and play it or directly teleports the player to the map they specified."
    ),
    array(
        "cmd" => "download &#60;map&nbsp;ID or URL&#62;",
        "aliases" => "/cpd",
        "descr" => "Downloads a map from this website. Map IDs can be found in the <a href=\"maplist.php\">map list</a> or on map pages, or just paste the URL of the map page (for example: <a href=\"map.php?id=1\">creativeparkour.net/map.php?id=1</a>)."
    ),
    array(
        "cmd" => "create",
        "aliases" => "new, build",
        "descr" => "Puts the player in a new map in creation mode. They can also come back to their last edited map and find maps in which their were invited by using this command."
    ),
    array(
        "cmd" => "leave",
        "aliases" => "quit, ragequit",
        "descr" => "If the player is in a map, teleports them where they were before playing CreativeParkour."
    ),
    array(
        "cmd" => "invite &#60;player&#62;",
        "descr" => "Invites someone to build a map with the command sender."
    ),
    array(
        "cmd" => "remove &#60;player&#62;",
        "descr" => "Disallows someone to build a map with the command sender."
    ),
    array(
        "cmd" => "contributors",
        "descr" => "Shows the list of players invited to build the map with <span class=\"commande\">/cp invite</span>, with buttons to easily remove them."
    ),
    array(
        "cmd" => "tp &#60;player&#62;",
        "descr" => "Teleports you to the specified player that is playing in a CreativeParkour map to spectate it."
    ),
    array(
        "cmd" => "settings",
        "aliases" => "preferences, options",
        "descr" => "Shows a GUI in which the player can set its preferred CreativeParkour settings (messages, notifications...)."
    ),
    array(
        "cmd" => "test",
        "aliases" => "validate",
        "descr" => "Puts the player in test mode in the map they are creating in order to validate it."
    ),
    array(
        "cmd" => "publish &#60;map&nbsp;name&#62;",
        "descr" => "If the map is validated, publishes the map on the server to allow other players to play it."
    ),
    array(
        "cmd" => "mapoptions",
        "descr" => "Displays to players creating parkour maps special options for their map."
    ),
    array(
        "cmd" => "spectator",
        "aliases" => "spec, spectate",
        "descr" => "Toggles spectator mode when a player plays a map."
    ),
    array(
        "cmd" => "ghost select",
        "id" => "ghost select",
        "aliases" => "ghost sel",
        "descr" => "Select ghosts to display."
    ),
    array(
        "cmd" => "ghost play",
        "id" => "ghost play",
        "aliases" => "ghost p",
        "descr" => "Starts playing the ghosts you selected."
    ),
    array(
        "cmd" => "ghost speed [multiplier]",
        "id" => "ghost speed",
        "aliases" => "ghost s",
        "descr" => "Makes ghosts you are watching going faster (2 times faster than usual by default, or the specified multiplier).<br />Fun facts: you can set <span class=\"cmd\">multiplier</span> to 0 to stop ghosts or to a negative value to make them walk backwards."
    ),
    array(
        "cmd" => "ghost rewind [seconds]",
        "id" => "ghost rewind",
        "aliases" => "ghost r",
        "descr" => "Puts ghosts 10 seconds in the past (or the specified number of seconds)."
    ),
    array(
        "cmd" => "ghost moment &#60;second&#62;",
        "id" => "ghost moment",
        "aliases" => "ghost m",
        "descr" => "Puts ghosts at the specified second."
    ),
    array(
        "cmd" => "ghost watch &#60;ghost ID&#62;",
        "id" => "ghost watch",
        "aliases" => "ghost w",
        "descr" => "Adds the ghost corresponding to the given ID to your ghost selection. Ghost IDs can be found by server admins when reviewing cheaters. If you don't know what it is, you should not use this command."
    ),
    array(
        "cmd" => "importsel",
        "aliases" => "importselection",
        "descr" => "Imports in a new CreativeParkour map an area selected with WorldEdit anywhere on your server. For example, this can be useful to convert your old parkours to CreativeParkour maps (you still have to place special signs for start, checkpoints, etc.)."
    ),
    array(
        "cmd" => "claim",
        "descr" => "Gives to players <a href=\"doc/rewards.php\">rewards</a> they obtained in parkour maps."
    ),
    array(
        "cmd" => "share",
        "aliases" => "upload",
        "descr" => "Sends the map the player is in to this website (only if the player is the map creator or has the \"creativeparkour.manage\" permission)."
    ),
    array(
        "cmd" => "edit",
        "descr" => "Turns back the map the player is in to edition mode if the player is its creator (or has the \"creativeparkour.manage\" permission)."
    ),
    array(
        "cmd" => "delete [map&nbsp;ID]",
        "descr" => "Deletes the map in which the player is or the one specified by the ID found with <span class=\"commande\">/cp getid</span> (only if the player is the map creator or has the \"creativeparkour.manage\" permission)."
    ),
    array(
        "cmd" => "register",
        "descr" => "Use this command to register and create an account on this website."
    ),
    
    array(
        "cmd" => "removetime &#60;player&#62; [all]",
        "aliases" => "deletetime",
        "descr" => "Deletes player's time in the current map (add <span class=\"commande\">all</span> after their name to delete their time in all the maps or the server). <strong>This cannot be undone, be careful!</strong>",
        "perm" => "restricted"
    ),
    array(
        "cmd" => "export",
        "descr" => "Creates a file containing data about the map the player is in. Maps can be imported by pasting <em>.cpmap</em> files in the \"Automatically import maps\" folder.",
        "perm" => "restricted"
    ),
    array(
        "cmd" => "managemaps",
        "descr" => "Displays a list of all the maps on the server with detailed information and quick actions.",
        "perm" => "restricted"
    ),
    array(
        "cmd" => "pin",
        "descr" => "Pins the map in which the player is in the map list (it will be at the top of the list when players do <span class=\"commande\">/cp	play</span>).",
        "perm" => "restricted"
    ),
    array(
        "cmd" => "unpin",
        "descr" => "Unpins the map in which the player is.",
        "perm" => "restricted"
    ),
    array(
        "cmd" => "getid [map&nbsp;name]",
        "descr" => "Displays the ID of the map where the player is or the one they typed.",
        "perm" => "restricted"
    ),
    array(
        "cmd" => "sync",
        "descr" => "Synchronizes downloadable map list, player names, player times and ghosts with this website. This is usually done automatically.",
        "perm" => "restricted"
    ),
    array(
        "cmd" => "noplates",
        "aliases" => "nopressureplates",
        "descr" => "Toggle usage of pressure plates as special blocks in the map you are. In fact, this command is used to disable the <a href=\"doc/configuration.php#game.pressure plates as special blocks\">\"pressure plates as special blocks\" option</a> in some specific maps, if there are problems.",
        "perm" => "restricted"
    ),
    array(
        "cmd" => "ban &#60;player&#62;",
        "descr" => "Prohibits the player to use the plugin.",
        "perm" => "restricted"
    ),
    array(
        "cmd" => "pardon &#60;player&#62;",
        "descr" => "Allows the player to use the plugin again.",
        "perm" => "restricted"
    ),
    array(
        "cmd" => "config",
        "aliases" => "configure",
        "descr" => "Used to configure the plugin on first use (or later if you want).",
        "perm" => "operator"
    ),
    array(
        "cmd" => "language &#60;language&nbsp;code&#62;",
        "aliases" => "lang",
        "descr" => "Sets CreativeParkour to the specified language (a language code like <span class=\"commande\">de</span> or <span class=\"commande\">deDE</span>). Obviously, only some languages are available. <a href=\"doc/languages.php\">Click here</a> for a list and information about helping translating the plugin to your language.",
        "perm" => "operator"
    ),
    array(
        "cmd" => "enable",
        "descr" => "Enables the plugin.",
        "perm" => "operator"
    ),
    array(
        "cmd" => "disable",
        "descr" => "Disables the plugin.",
        "perm" => "operator"
    )
);

function afficherTableauCommandes($perm = null)
{
    global $commandes;
    // Recherche de s'il faut les alias ou pas
    foreach ($commandes as $c) {
        if (((! $perm && ! $c["perm"]) || $perm == $c["perm"]) && $c["aliases"])
            $aliases = true;
    }
    // Affichage
    echo '<table class="tableau"><thead><tr><th>Command</th>';
    if ($aliases)
        echo '<th>Aliases</th>';
    echo '<th>Description</th></tr></thead><tbody>';
    
    foreach ($commandes as $c) {
        if ((! $perm && ! $c["perm"]) || $perm == $c["perm"]) {
            if ($c["id"])
                $id = $c["id"];
            else {
                $espace = strpos($c["cmd"], " ");
                $id = $espace > 0 ? substr($c["cmd"], 0, strpos($c["cmd"], " ")) : $c["cmd"];
            }
            echo '<tr id="' . htmlspecialchars($id) . '"><td>/cp ' . $c["cmd"] . '</td>';
            if ($aliases)
                echo '<td>' . $c["aliases"] . '</td>';
            echo '<td>' . $c["descr"] . '</td></tr>';
        }
    }
    echo '</tbody></table>';
}
?>
<section class="texte">
	<h1>Commands</h1>
	<p>
		The plugin uses only one command: <span class="commande">/cp</span>
		(or <span class="commande">/creativeparkour</span>, or <span
			class="commande">/cpk</span>), but there are many possible arguments.<br />
		In this table (and in Minecraft in general), <em>&#60;argument&#62;</em>
		represents a required argument and <em>[argument]</em> represents an
		optional argument.
	</p>
	<?php afficherTableauCommandes (); ?>

	<h2 id="WorldEdit">WorldEdit commands</h2>
	<p>
		WorldEdit can be used in the map builder to help you creating
		parkours. <a href="doc/map-creation.php#WorldEdit">Click here to read
			how to use it</a>.<br /> Available WorldEdit commands are <span
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
		<br /> <a href="http://wiki.sk89q.com/wiki/WorldEdit/Reference"
			target="_blank">Click here</a> to see WorldEdit's documentation about
		these commands. <br />You can find all the block IDs on websites like
		<a rel="nofollow" target="_blank"
			href="http://minecraft-ids.grahamedgecombe.com/">this one</a>.
	</p>

	<h2>Restricted commands</h2>
	<p>
		These commands require the <a href="doc/permissions.php">"creativeparkour.manage"
			permission</a>:
	</p>
	<?php afficherTableauCommandes ("restricted"); ?>

	<h2>Operator commands</h2>
	<p>Only operators can perform these commands:</p>
	<?php afficherTableauCommandes ("operator"); ?>
</section>
<br />
<?php
documentation(true);
include_once '../includes/bas.php';
?>