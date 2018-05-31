<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
body {
	background-image:
		url('https://creativeparkour.net/images/fond-erreur.jpg');
	background-size: 100%;
	background-repeat: no-repeat;
	background-color: rgb(101, 138, 204);
	font-family: 'Trebuchet MS', Helvetica, Arial, sans-serif;
}

p {
	background-color: rgba(255, 255, 255, 0.8);
	margin: 40px auto auto auto;
	padding: 30px;
	font-size: 1.1em;
	width: 800px;
	border-radius: 20px;
}
</style>
</head>
<body>
	<p>
		<?php echo $messageErreur; ?><br />
		You can still read CreativeParkour's presentation and download the
		plugin on <a target="_blank"
			href="http://dev.bukkit.org/bukkit-plugins/creativeparkour/">Bukkit</a>
		or <a target="_blank"
			href="https://www.spigotmc.org/resources/creativeparkour.17303/">Spigot</a>.
	</p>
</body>
</html>