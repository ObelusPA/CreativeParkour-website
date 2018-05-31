<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$titre = "Administration";
require_once 'haut-admin.php';
?>
<section class="texte">
	<h1>Administration</h1>
	<p>
		<a href="user/admin!/fantomes.php">Validation des fantômes</a><?php afficherNbRouge($nbFantomesAFaire); ?><br />
		<a href="user/admin!/erreurs.php">Erreurs</a><?php afficherNbRouge($nbErreurs); ?><br />
		<a href="user/admin!/signalements.php">Signalements de fantômes</a><?php afficherNbRouge($nbSignalements); ?><br />
		<a href="user/admin!/traductions.php">Traductions</a><br />
		Bannissements<br /> <a href="user/admin!/recaptcha-test.php">Test
			ReCaptcha</a>
	</p>

	<h2>Statistiques globales</h2>
	<p>
		<strong>Cette semaine :</strong><br />
	<?php
// Mise à jour des stats
$rustart = getrusage();

echo "<!--";
include ("../../cron/calcul-stats.php");
echo "-->\n";

$reponse = $bdd->query("SELECT element, valeur FROM statsSemaine") or die("SQL error 29");
while ($donnees = $reponse->fetch()) {
    $statistiques[$donnees["element"]] = $donnees["valeur"];
}
$reponse->closeCursor();

echo '&nbsp;&bull; Joueurs : ' . htmlspecialchars($statistiques["nbJoueursCP"]) . '<br />';
echo '&nbsp;&bull; Parkours joués : ' . htmlspecialchars($statistiques["parkoursTentes"]) . '<br />';
echo '&nbsp;&bull; Parkours réussis : ' . htmlspecialchars($statistiques["parkoursReussis"]) . '<br />';
echo '&nbsp;&bull; Minutes jouées : ' . htmlspecialchars(round($statistiques["secondesJouees"] / 60, 1)) . ' (' . htmlspecialchars(round($statistiques["secondesJouees"] / 60 / 60, 1)) . ' heures)<br />';
echo '&nbsp;&bull; Sauts : ' . htmlspecialchars($statistiques["nbSauts"]) . '<br />';
?>
	<br /> <br /> <strong>Bordel :</strong><br />
	<?php
// Maps valides
$reponse = $bdd->query("SELECT COUNT(*) as nb FROM maps WHERE etat = 'disponible'") or die("SQL error 31");
echo '&nbsp;&bull; Maps valides : ' . htmlspecialchars($reponse->fetch()["nb"]) . '<br />';
$reponse->closeCursor();

// Serveurs
$reponse = $bdd->query("SELECT COUNT(*) as nb FROM serveurs WHERE etat = 'public' OR etat = 'prive'") or die("SQL error 37");
$nb1 = $reponse->fetch()["nb"];
$reponse->closeCursor();
$reponse = $bdd->query("SELECT COUNT(*) as nb FROM serveurs") or die("SQL error 45");
$nb2 = $reponse->fetch()["nb"];
$reponse->closeCursor();
echo '&nbsp;&bull; Serveurs valides/total : ' . htmlspecialchars($nb1 . '/' . $nb2) . '<br />';

// Serveurs uniques
$reponse = $bdd->query("SELECT COUNT(DISTINCT uuidServ) as nb FROM statistiques") or die("SQL error 51");
echo '&nbsp;&bull; Serveurs uniques : ' . htmlspecialchars($reponse->fetch()["nb"]) . ' (depuis le 19 avril 2016)<br />';
$reponse->closeCursor();

// Utilisateurs
$reponse = $bdd->query("SELECT COUNT(*) as nb FROM utilisateurs") or die("SQL error 51");
echo '&nbsp;&bull; Utilisateurs : ' . htmlspecialchars($reponse->fetch()["nb"]) . '<br />';
$reponse->closeCursor();

// Fantômes
$reponse = $bdd->query("SELECT COUNT(*) as nb FROM fantomes WHERE etat = 'valide'") or die("SQL error 61");
$nb1 = $reponse->fetch()["nb"];
$reponse->closeCursor();
$reponse = $bdd->query("SELECT COUNT(*) as nb FROM fantomes WHERE etat = 'invalide'") or die("SQL error 64");
$nb2 = $reponse->fetch()["nb"];
$reponse->closeCursor();
echo '&nbsp;&bull; Fantômes valides/invalides : ' . htmlspecialchars($nb1 . '/' . $nb2) . '<br />';

// Votes
$reponse = $bdd->query("SELECT COUNT(*) as nb FROM votes WHERE etat = 'valide'") or die("SQL error 70");
$nb1 = $reponse->fetch()["nb"];
$reponse->closeCursor();
$reponse = $bdd->query("SELECT COUNT(*) as nb FROM votes") or die("SQL error 73");
$nb2 = $reponse->fetch()["nb"];
$reponse->closeCursor();
echo '&nbsp;&bull; Votes validés/total : ' . htmlspecialchars($nb1 . '/' . $nb2) . '<br />';

// Connexions
/*
 * $reponse = $bdd->query ( "SELECT COUNT(*) as nb FROM connexions WHERE page LIKE '%/api/%'" ) or die ( "SQL error 75" );
 * $nb1 = $reponse->fetch () ["nb"];
 * $reponse->closeCursor ();
 * $reponse = $bdd->query ( "SELECT COUNT(*) as nb FROM connexions" ) or die ( "SQL error 77" );
 * $nb2 = $reponse->fetch () ["nb"];
 * $reponse->closeCursor ();
 * echo '&nbsp;&bull; Connexions API/total : ' . htmlspecialchars ( $nb1 . '/' . $nb2 . ' (' . round ( $nb1 / $nb2 * 100, 1 ) ) . ' % API)<br />';
 */

// IPs uniques
/*
 * $reponse = $bdd->query ( "SELECT COUNT(DISTINCT ip) as nb FROM connexions" ) or die ( "SQL error 83" );
 * echo '&nbsp;&bull; IPs uniques : ' . htmlspecialchars ( $reponse->fetch () ["nb"] ) . '<br />';
 * $reponse->closeCursor ();
 */

// Dernières stats reçues pour chaque serveur
$reponse = $bdd->query("SELECT uuidServ, infosPlugin, date, versionServeur, langue, ipConnexion, commandes, onlineMode, nbJoueursCP, secondesJouees, parkoursTentes, parkoursReussis, nbSauts
			FROM statistiques 
			WHERE idServ <> 1 AND date BETWEEN DATE_SUB(NOW(),INTERVAL 1 MONTH) and NOW()
			ORDER BY date DESC") or die("SQL error 107");
while ($d = $reponse->fetch()) {
    if (! in_array($d["uuidServ"], $serveurs)) { // On ne veut que le dernier enregistrement de chaque serveur
        $serveurs[] = $d["uuidServ"];
        $v = str_ireplace("CreativeParkour v", "", $d["infosPlugin"]);
        if (strtotime($d["date"]) >= time() - 60 * 60 * 24 * 7) { // Si c'était pendant la semaine
            $w[$v] ++;
            $languesS[$d["langue"]] ++;
            $pays[Locale::getDisplayRegion("-" . getPaysIP($d["ipConnexion"]), "en")] ++;
            $onlineModeS[$d["onlineMode"]] ++;
            
            // Recherche de la version du serveur
            preg_match("#\(MC: ([.\d]+)\)#i", $d["versionServeur"], $matches);
            $version = $matches[1];
            $versionsS[$version] ++;
            if (strtotime($d["date"]) >= time() - 60 * 60 * 24) { // Si c'était pendant les dernières 24 heures
                $j[$v] ++;
                $versionsJ[$version] ++;
            }
        }
    }
    
    // Pas les dernières de chaque serveur :
    if (strtotime($d["date"]) >= time() - 60 * 60 * 24 * 31) { // Si c'était pendant le mois
        $date = date("D d/m/Y", strtotime($d["date"]));
        if ($d["nbJoueursCP"] > 0) {
            $joueurs[$date] += $d["nbJoueursCP"];
        }
        if (! in_array($d["uuidServ"], $servsJours[$date])) { // Pour n'avoir chaque serveur qu'une fois par jour
            $serveursM[$date] ++;
            $servsJours[$date][] = $d["uuidServ"];
        }
        
        if (strtotime($d["date"]) >= time() - 60 * 60 * 24 * 7) { // Si c'était pendant la semaine
                                                                       
            // Commandes
            $commandesArr = explode(";", $d["commandes"]);
            foreach ($commandesArr as $commande) {
                $explode = explode(":", $commande);
                $commandesS[$explode[0]] += (int) $explode[1];
            }
        }
    }
}
$reponse->closeCursor();

$reponse = $bdd->query("SELECT SUM(secondesJouees) AS secondesJouees, SUM(parkoursTentes) AS parkoursTentes, SUM(parkoursReussis) AS parkoursReussis, SUM(nbSauts) AS nbSauts FROM statistiques") or die("SQL error 152");
$d = $reponse->fetch();
$secondesJouees = $d["secondesJouees"];
$parkoursTentes = $d["parkoursTentes"];
$parkoursReussis = $d["parkoursReussis"];
$nbSauts = $d["nbSauts"];
$reponse->closeCursor();

echo '&nbsp;&bull; Total des heures jouées : ' . htmlspecialchars(round($secondesJouees / 3600, 1)) . '<br />';
echo '&nbsp;&bull; Total des parkours tentés/réussis : ' . htmlspecialchars($parkoursTentes . "/" . $parkoursReussis . " (" . round($parkoursReussis / $parkoursTentes * 100, 1) . " % réussis)") . '<br />';
echo '&nbsp;&bull; Total des sauts : ' . htmlspecialchars($nbSauts) . '<br />';

// Même chose, mais pour avoir le premier enregistrement de chaque serveur (que sur le mois)
$reponse = $bdd->query("SELECT uuidServ, date
			FROM statistiques
			ORDER BY date ASC") or die("SQL error 163");
while ($d = $reponse->fetch()) {
    if (! in_array($d["uuidServ"], $serveurs2)) { // On ne veut que le dernier enregistrement de chaque serveur
        $serveurs2[] = $d["uuidServ"];
        if (strtotime($d["date"]) >= time() - 60 * 60 * 24 * 30) { // Si c'était pendant le mois
            $nveauxServeursM[date("D d/m/Y", strtotime($d["date"]))] ++;
        }
    }
}
$reponse->closeCursor();

afficherCamembert($j, "Versions du jour");
afficherCamembert($w, "Versions de la semaine");
afficherCamembert($onlineModeS, "Online mode cette semaine");
echo "<br />";
afficherCamembert($versionsJ, "Versions Minecraft du jour");
afficherCamembert($versionsS, "Versions Minecraft de la semaine");

afficherBarres($languesS, "Langues de la semaine", "Serveurs");
echo "<br />";

afficherCourbe(array_reverse($joueurs), "Joueurs par jour ce mois-ci", "Joueurs");
echo "<br />";

afficherCourbe(array_reverse($serveursM), "Serveurs par jour ce mois-ci", "Serveurs");
echo "<br />";

afficherCourbe($nveauxServeursM, "Nouveaux serveurs par jour ce mois-ci", "Nouv. serveurs");
echo "<br />";

// Séparation des commandes qui n'ont été faite qu'une fois
foreach ($commandesS as $commande => $nb) {
    if ($nb > 4)
        $commandesS2[$commande] = $nb;
    else
        $autresCommandes[$commande] = $nb;
}
afficherBarres($commandesS2, "Commandes de la semaine", "Utilisations", 960, count($commandesS2) * 23);
echo "<p><strong>Autres commandes :</strong> ";
$virgule = false;
foreach ($autresCommandes as $commande => $nb) {
    if ($virgule) {
        echo ", ";
    }
    if ($commande) {
        echo htmlspecialchars($commande);
        $virgule = true;
    }
}
echo "</p>";

// Autres plugins installés
$reponse = $bdd->query("SELECT plugins FROM serveurs WHERE plugins <> ''") or die("SQL error 218");
while ($d = $reponse->fetch()) {
    if ($d["plugins"]) {
        $pluginsArr = explode(",", $d["plugins"]);
        foreach ($pluginsArr as $plugin) {
            if ($plugin !== "CreativeParkour")
                $plugins[$plugin] ++;
        }
    }
}
$reponse->closeCursor();
// Séparation des plugins pas beaucoup installés
foreach ($plugins as $plugin => $nb) {
    if ($nb > 10)
        $plugins2[$plugin] = $nb;
    else if ($nb > 1)
        $autresPlugins[$plugin] = $nb;
}
arsort($autresPlugins);
afficherBarres($plugins2, "Plugins installés", "Serveurs", 960, count($plugins2) * 23);
echo '<p style="font-size: 0.8em"><strong>Autres plugins :</strong> ';
$virgule = false;
$nbPreced = 0;
foreach ($autresPlugins as $plugin => $nb) {
    if ($virgule) {
        echo ", ";
    }
    if ($plugin) {
        if ($nb !== $nbPreced)
            echo '<strong style="color:#56bf56">' . $nb . ' ➤ </strong>';
        $nbPreced = $nb;
        echo htmlspecialchars($plugin);
        $virgule = true;
    }
}
echo "</p>";

afficherGraph("GeoChart", $pays, "Pays", "{width:960}", false, "Serveurs");

echo "<p><strong>Statistiques des maps partagées :</strong><br />";
// Remplissage d'une liste des maps partagées
$reponse = $bdd->query("SELECT uuid FROM maps WHERE etat = 'disponible'") or die("SQL error 178");
while ($d = $reponse->fetch()) {
    $mapsPartagees[] = $d["uuid"];
}
$reponse->closeCursor();

// On regarde les maps sur chaque serveur
$reponse = $bdd->query("SELECT uuid, nom, maps
				FROM serveurs") or die("SQL error 229");
while ($d = $reponse->fetch()) {
    if ($d["maps"]) {
        foreach (explode(";", $d["maps"]) as $map) {
            $maps[$map] ++;
        }
        $arr = explode(";", $d["maps"]);
        // On n'affiche pas les serveurs avec qu'une map...
        if (count($arr) > 1) {
            $nb1 = 0; // Maps tléchargées
            $nb2 = 0; // Autres
            foreach ($arr as $u) {
                if (in_array($u, $mapsPartagees))
                    $nb1 ++;
                else
                    $nb2 ++;
            }
            $autresMaps += $nb2;
            if (count($arr) >= 10) {
                $mapsServeurs[$d["uuid"] . ":" . $d["nom"]] = count($arr);
                $mapsServeurs1[$d["uuid"] . ":" . $d["nom"]] = $nb1 . "/" . $nb2;
            }
        }
    }
}
$reponse->closeCursor();
// Recherche des noms des maps et d'autres infos et affichage
echo '</p><table class="tableau tableauCompresse" style="font-size: 0.8em"><thead><tr><th>Nom</th><th>ID</th><th>UUID</th><th>Serveurs</th><th>Difficulté</th><th>Qualité</th><th>Tentatives</th><th>Fantômes</th><th>Points</th></tr></thead><tbody>';
$reponse = $bdd->prepare("SELECT id, uuid, nom, difficulte, qualite, tentatives, points FROM maps WHERE etat = 'disponible' ORDER BY points DESC");
$reponse->execute() or die("SQL error 298");
$donnees = $reponse->fetchAll();
$reponse->closeCursor();
foreach ($donnees as $d) {
    $nb = $maps[$d["uuid"]];
    // Recherche du nombre de fantômes dans la map
    $reponse = $bdd->prepare("SELECT COUNT(*) as nb FROM fantomes WHERE uuidMap = :uuid");
    $reponse->execute(array(
        "uuid" => $d["uuid"]
    )) or die("SQL error 308");
    $nbFantomes = $reponse->fetch()["nb"];
    $reponse->closeCursor();
    if ($d)
        echo '<tr><td><a href="map.php?id=' . htmlspecialchars($d["id"]) . '">' . htmlspecialchars($d["nom"]) . "</a></td><td>" . htmlspecialchars($d["id"]) . "</td><td>" . htmlspecialchars($d["uuid"]) . "</td><td>" . htmlspecialchars($nb) . "</td><td>" . htmlspecialchars($d["difficulte"]) . "</td><td>" . htmlspecialchars($d["qualite"]) . "</td><td>" . htmlspecialchars(array_sum(tentativesToArray($d["tentatives"]))) . "</td><td>" . htmlspecialchars($nbFantomes) . "</td><td>" . htmlspecialchars($d["points"]) . "</td></tr>";
}

// Nombre de maps par serveur
echo '</tbody></table><p><strong>Maps par serveur (téléchargées/autres, 10 minimum) :</strong></p><p style="font-size: 0.8em; column-count: 4;">';
arsort($mapsServeurs);
foreach ($mapsServeurs as $serv => $nb) {
    $nb = $mapsServeurs1[$serv];
    echo "&nbsp;&bull; " . htmlspecialchars(explode(":", $serv)[1]) . " : " . htmlspecialchars($nb) . "<br />";
}
echo "</p>";

function rutime($ru, $rus, $index)
{
    return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000)) - ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
}

echo "<p>Maps pas partagées : " . htmlspecialchars($autresMaps) . "<br /><br />";

$ru = getrusage();
echo "This process used " . rutime($ru, $rustart, "utime") . " ms for its computations<br />";
echo "It spent " . rutime($ru, $rustart, "stime") . " ms in system calls<br />";
?>
	</p>
</section>
<?php include_once '../../includes/bas.php'; ?>