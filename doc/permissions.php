<?php
require_once '../includes/haut-global.php';
$titre = "Permisssions";
$suffixeTitre = "documentation";
$metaDescription = "List of permissions available in the CreativeParkour Bukkit plugin.";
require_once '../includes/haut-html.php';
documentation();
?>
<section class="texte">
	<h1>Permissions</h1>
	<p>
		Here is the list of all the permissions used by the CreativeParkour
		Bukkit plugin. If you want other permissions to make the plugin more
		configurable, please <a href="contact.php">contact the author</a>.
	</p>
	<p id="warn" class="avertissement">
		Permissions for which the default value is "everybody" are by default
		given to all the players on your server.<br /> <strong>To remove them
			from a player, you have to explicitly tell your permission plugin
			that this player must not have the permission.</strong><br /> For
		example, with <a
			href="http://dev.bukkit.org/bukkit-plugins/permissionsex/"
			target="_blank">PermissionsEx</a>, to prohibit a player to create
		parkour maps, you have to give them the <span class="commande"
			style="white-space: nowrap;">-creativeparkour.create</span>
		permission, with a dash before the permission (so in your permission
		list, it will look like <span class="commande"
			style="white-space: nowrap;">- -creativeparkour.create</span> because
		of the other dash to introduce the item of the list).
	</p>
	<p class="avertissement">If you remove some permissions to players, it
		is recommended to explicitly give them the other permissions you want
		them to have.</p>
	<p class="avertissement">
		If you are using a permission plugin, it is recommended to install <a
			href="http://dev.bukkit.org/bukkit-plugins/vault/" target="_blank">Vault</a>
		to prevent permission issues (especially with WorldEdit when building
		parkour maps).
	</p>
	<table class="tableau">
		<thead>
			<tr>
				<th>Permission</th>
				<th>Default&nbsp;value</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>creativeparkour.*</td>
				<td>Operators</td>
				<td>Gives access to all CreativeParkour commands and permissions
					(configuration commands included!).</td>
			</tr>
			<tr>
				<td>creativeparkour.play</td>
				<td>Everybody</td>
				<td>Allows players to play parkour maps. Players without this
					permission cannot use the CreativeParkour plugin.</td>
			</tr>
			<tr>
				<td>creativeparkour.create</td>
				<td>Everybody</td>
				<td>Allows players to create parkour maps.</td>
			</tr>
			<tr>
				<td>creativeparkour.invite</td>
				<td>Everybody</td>
				<td>Allows players to invite others to build maps with them in
					creation mode.</td>
			</tr>
			<tr id="creativeparkour.infinite">
				<td>creativeparkour.infinite</td>
				<td>Everybody</td>
				<td>Allows players to create an infinite number of maps. Check out <a
					href="doc/configuration.php#map creation.maps per player limit">"map
						creation.maps per player limit" in the plugin configuration</a> to
					set the limit for those who do not have the permission.
				</td>
			</tr>
			<tr>
				<td>creativeparkour.worldedit</td>
				<td>Everybody</td>
				<td>Allows players to use the WorldEdit wand when creating a map.</td>
			</tr>
			<tr>
				<td>creativeparkour.download</td>
				<td>Operators</td>
				<td>Allows players to download maps from this website.</td>
			</tr>
			<tr>
				<td>creativeparkour.share</td>
				<td>Everybody</td>
				<td>Allows players to share their maps with the community on this
					website (<a href="http://creativeparkour.net/doc/add-map.php">how?</a>).
				</td>
			</tr>
			<tr>
				<td>creativeparkour.ghosts.save</td>
				<td>Everybody</td>
				<td>Ghosts will be created when players that have this permission
					finish a parkour.</td>
			</tr>
			<tr>
				<td>creativeparkour.ghosts.see</td>
				<td>Everybody</td>
				<td>Allows players to see other players' ghosts when playing maps.</td>
			</tr>
			<tr>
				<td>creativeparkour.spectate</td>
				<td>Everybody</td>
				<td>Allows players to use spectator mode when playing maps.</td>
			</tr>
			<tr>
				<td>creativeparkour.rate.difficulty</td>
				<td>Everybody</td>
				<td>Allows players to rate maps' difficulty.</td>
			</tr>
			<tr>
				<td>creativeparkour.rate.quality</td>
				<td>Everybody</td>
				<td>Allows players to rate maps' quality.</td>
			</tr>
			<tr>
				<td>creativeparkour.createSigns</td>
				<td>Operators</td>
				<td>Allows players to place <a href="doc/lobby-signs.php">lobby
						signs</a>.
				</td>
			</tr>
			<tr>
				<td>creativeparkour.manage</td>
				<td>Operators</td>
				<td>Allows players to pin, export, edit and delete maps, to invite
					and remove players without being the map creator, and ban and
					pardon players from CreativeParkour.</td>
			</tr>
		</tbody>
	</table>
</section>
<br />
<?php
documentation(true);
include_once '../includes/bas.php';
?>