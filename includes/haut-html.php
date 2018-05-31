<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="icon" type="image/png" href="favicon.png" />
<meta name="theme-color" content="#ffc005">
<base
	href="<?php echo ($_SERVER["HTTPS"] ? "https" : "http");?>://creativeparkour.net/">
<link rel="stylesheet" href="style.css">
<?php if ($redirectionDelai) { echo '<meta http-equiv="refresh" content="5;' . htmlspecialchars($redirectionDelai) . '" />'; }?>
<title><?php if ($titre) echo $titre . " &bull; ";?>CreativeParkour<?php if ($suffixeTitre) echo " " . $suffixeTitre;?></title>
<?php
if ($metaDescription) {
    echo '<meta name="description" content="' . htmlspecialchars($metaDescription) . '" />' . "\n";
}
if ($noIndex) {
    echo '<meta name="robots" content="noindex">' . "\n";
}
if ($canonical) {
    echo '<link rel="canonical" href="' . htmlspecialchars($canonical) . '"/>' . "\n";
}
?>

<?php if (!$pasAnalytics && $_SESSION ["utilisateur"]->id != 1) { ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-67851045-3', 'auto');
  ga('require', 'linkid');
  ga('send', 'pageview');

</script>
<?php } ?>
</head>
<body>
	<div id="corps">
		<header>
			<a href="" title="Main page"><img alt="CreativeParkour"
				src="images/bannière.png"></a>
		</header>
		<nav>
			<a href="">Home</a> <a href="doc/play.php">Play</a> <a
				href="maplist.php">Parkour map list</a> <a href="doc">Documentation</a> <?php echo connecte()? '<a href="user">Preferences</a> <a href="user/logout.php">Log out</a>' : '<a href="user/login.php?return=' . htmlspecialchars(strpos($_SERVER ['REQUEST_URI'], "login.php") === false ? $_SERVER ['REQUEST_URI'] : "/") . '">Log in</a>';?>
		</nav>
		<?php

// Affichage des messages verts s'il y en a

if ($_SESSION["msgOK"]) {
    
    echo '<p class="msg msgOK">';
    
    foreach ($_SESSION["msgOK"] as $msg) {
        
        echo htmlspecialchars($msg) . '<br />';
    }
    
    $_SESSION["msgOK"] = array(); // Vidange des erreurs après les avoir affichées
    
    echo '</p>';
}

// Affichage des erreurs s'il y en a

if ($_SESSION["erreurs"]) {
    
    echo '<p class="msg erreurs">';
    
    foreach ($_SESSION["erreurs"] as $erreur) {
        
        echo htmlspecialchars($erreur) . '<br />';
    }
    
    $_SESSION["erreurs"] = array(); // Vidange des erreurs après les avoir affichées
    
    echo '</p>';
}

?>