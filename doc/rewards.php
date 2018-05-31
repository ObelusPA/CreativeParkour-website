<?php
require_once '../includes/haut-global.php';

$titre = "Custom rewards";
$suffixeTitre = "documentation";
$metaDescription = "Configure CreativeParkour ro give custom rewards to players in some parkour maps.";
require_once '../includes/haut-html.php';
documentation();
?>
<section class="texte">
	<h1>Custom rewards</h1>
	<p>
		CreativeParkour can be configured to give custom rewards to players
		that finish some parkour maps. Available rewards are items (enchanted
		if you want), XP, commands and money (if you have a Vault-compatible
		economy plugin, see below).<br /> These rewards are configured in the
		<em>rewards.yml</em> file, in the <em><em>&#60;your&nbsp;server&#62;/plugins/CreativeParkour</em></em>
		folder.
	</p>

	<h2>Configuration</h2>
	<p>
		Open <em>rewards.yml</em> and you will see these examples:
		<code>
			example1:<br /> &nbsp;&nbsp;type: ITEM<br /> &nbsp;&nbsp;map: 2<br />
			&nbsp;&nbsp;once: true<br /> &nbsp;&nbsp;amount: 1<br />
			&nbsp;&nbsp;itemname: IRON_HELMET<br /> &nbsp;&nbsp;itemdata: 0<br />
			&nbsp;&nbsp;itemenchants:<br /> &nbsp;&nbsp;- OXYGEN:1<br />
			&nbsp;&nbsp;- DURABILITY:2<br /> example2:<br /> &nbsp;&nbsp;type: XP<br />
			&nbsp;&nbsp;map: all<br /> &nbsp;&nbsp;once: false<br />
			&nbsp;&nbsp;amount: 10<br /> example3:<br /> &nbsp;&nbsp;type: MONEY<br />
			&nbsp;&nbsp;map: 0,6<br /> &nbsp;&nbsp;once: true<br />
			&nbsp;&nbsp;amount: 150<br /> &nbsp;&nbsp;cooldown: 60<br />
			example4:<br /> &nbsp;&nbsp;type: COMMAND<br /> &nbsp;&nbsp;map: all<br />
			&nbsp;&nbsp;once: false<br /> &nbsp;&nbsp;command: effect @player
			REGENERATION 10 1<br /> &nbsp;&nbsp;displayname: Regeneration II for
			10 seconds<br />
		</code>
		<br /> The file is divided in 4 sections, 1 for each reward. You have
		to choose an unique name for each reward (here <em>example1</em>, <em>example2</em>,
		<em>example3</em> and <em>example4</em>) and write separate sections
		for each reward. You can create as many sections (and rewards) as you
		want.<br />You can copy and rename these examples and change their
		properties to create your custom rewards (these examples are ignored
		by the plugin).
	</p>
	<h3>Reward properties</h3>
	<p>Each reward has several properties to choose what to give to players
		and when. Here is a list that explains these properties:</p>
	<table class="tableau">
		<thead>
			<tr>
				<th>Property</th>
				<th>Value</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>type</td>
				<td>ITEM/XP/COMMAND/MONEY</td>
				<td>The type of the reward. Use <em>ITEM</em> to give items to
					players, <em>XP</em> to give experience, <em>COMMAND</em> to
					execute a server command or <em>MONEY</em> to give money with an
					economy plugin.
				</td>
			</tr>
			<tr>
				<td>map</td>
				<td>Map IDs separated by commas or <span class="commande">all</span></td>
				<td>IDs of parkour maps where you want players to get the reward
					when they reach the end of them. These IDs can be found with <span
					class="commande">/cp getid</span> or <span class="commande">/cp
						managemaps</span>. Write a comma-separated ID list (like in the <em>example3</em>
					section above) or a single map ID (like in <em>example1</em>).<br />
					If you want to give the reward in all the maps, set this property
					to <span class="commande">all</span>.
				</td>
			</tr>
			<tr>
				<td>once</td>
				<td>true/false</td>
				<td>Set <em>true</em> if you want to give the reward only once for
					each player that finishes the map(s) or <em>false</em> to do it
					each time they finish it.
				</td>
			</tr>
			<tr>
				<td>amount</td>
				<td>Number</td>
				<td>For <em>ITEM</em> rewards: amount of items given to players (for
					example, write 64 to give 1 stack).<br /> For <em>XP</em> rewards:
					number of XP points given to players (these are not levels, <a
					href="http://minecraft.gamepedia.com/Experience" target="_blank">see
						the Minecraft Wiki for details</a>).<br /> For <em>MONEY</em>
					rewards: amount of money deposited in player's account.
				</td>
			</tr>
			<tr>
				<td>cooldown</td>
				<td>Number</td>
				<td>Minutes players have to wait before obtaining the reward again.
					"once" must be set to <em>false</em> if you use this property.
				</td>
			</tr>
			<tr>
				<td>itemname</td>
				<td>String</td>
				<td>Only for <em>ITEM</em> rewards.<br />Name of the item you want
					to give to players. <strong>Exact item names from <a
						href="https://hub.spigotmc.org/javadocs/bukkit/org/bukkit/Material.html"
						target="_blank">this list</a> must be used.
				</strong></td>
			</tr>
			<tr>
				<td>itemdata</td>
				<td>Number</td>
				<td>Optional and only for <em>ITEM</em> rewards.<br />Item data
					value.
				</td>
			</tr>
			<tr>
				<td>itemenchants</td>
				<td>List</td>
				<td>Optional and only for <em>ITEM</em> rewards.<br />List of
					enchants to put on the item. Each enchant should be written like
					this: <span class="commande">&#60;enchant&nbsp;name&#62;:&#60;enchant&nbsp;level&#62;</span>
					(see the first example above). <strong>Exact enchant names from <a
						href="https://hub.spigotmc.org/javadocs/bukkit/org/bukkit/enchantments/Enchantment.html"
						target="_blank">this list</a> must be used. Enchant level must be
						between 1 and 5.
				</strong>
				</td>
			</tr>
			<tr>
				<td>command</td>
				<td>String</td>
				<td>Only for <em>COMMAND</em> rewards.<br />Command to execute when
					the player claims the reward (it must be a console command, without
					the first <span class="cmd">/</span>). Every <span class="cmd">@player</span>
					tag in your command will be replaced by player's name.<br /> <strong>Use
						COMMAND rewards with care, they can be dangerous!</strong>
				</td>
			</tr>
			<tr>
				<td>displayname</td>
				<td>String</td>
				<td>Required for <em>COMMAND</em> rewards.<br />Short text displayed
					to players to describe the reward when they get it.
				</td>
			</tr>
		</tbody>
	</table>
	<div class="avertissement">
		Never indent YML files with tab, only use spaces like in the example.<br />It
		is always safer to copy examples and change names and properties.
	</div>

	<h3>
		Using <em>MONEY</em> rewards
	</h3>
	<p>
		<em>MONEY</em> rewards use third-party economy plugins. They must be
		compatible with <a href="https://dev.bukkit.org/bukkit-plugins/vault/"
			target="_blank">Vault</a> and you have to install the Vault plugin on
		your server.<br /> These plugins are compatible with <em>MONEY</em>
		rewards if you install Vault: iConomy, BOSEconomy, EssentialsEcon,
		3Co, MultiCurrency, MineConomy, eWallet, EconXP, CurrencyCore,
		CraftConomy, AEco, Gringotts.<br /> <br /> You can customize the way
		CreativeParkour displays money amounts with the <a
			href="doc/configuration.php#rewards.currency"><em>currency</em>
			option in <em>configuration.yml</em></a>.
	</p>
</section>
<?php
documentation(true);
include_once '../includes/bas.php';
?>