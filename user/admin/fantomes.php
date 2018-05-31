<?php
if ($_GET["triche"] == 1) {
    include_once '../../cron/verif-fantomes.php';
    header("Location: fantomes.php");
}

$titre = "Validation des fantômes";
require_once 'haut-admin.php';

$validationActivee = validationFantomesActivee();
// Changement de la valeur si c'est demandé ou si on commence ou termine de valider tous les fantômes
if ($_GET["changerActivation"] == 1 || (! $validationActivee && $nbFantomesAFaire > 0) || ($validationActivee && $nbFantomesAFaire == 0)) {
    $req = $bdd->prepare("UPDATE config SET valeur = :val WHERE cle = 'validation-fantomes'");
    $req->execute(array(
        'val' => ! $validationActivee ? "true" : "false"
    )) or die("SQL error 15");
    $req->closeCursor();
    header("Location: fantomes.php");
    die();
}

// Traitement du formulaire
if ($_POST["ok"]) {
    if (count($_POST["sélection"]) > 0) {
        $strReq = "UPDATE fantomes SET etat = :etat, triche = :triche WHERE";
        $arrayReq = array(
            'etat' => $_POST["action"] == "valider" ? 'valide' : 'invalide',
            'triche' => $_POST["action"] == "triche" ? 1 : 0
        );
        $i = 0;
        
        foreach ($_POST["sélection"] as $idFantome) {
            // Si on valide le fantôme, il faut marquer les éventuels précédents comme périmés
            if ($_POST["action"] == "valider") {
                // Récupération de l'UUID de la map et du joueur pour ce fantôme
                $reponse = $bdd->prepare("SELECT uuidMap, uuidJoueur, nomJoueur FROM fantomes WHERE id = :id");
                $reponse->execute(array(
                    "id" => $idFantome
                )) or die("SQL error 26");
                $d = $reponse->fetch();
                $uuidMap = $d["uuidMap"];
                $uuidJoueur = $d["uuidJoueur"];
                $reponse->closeCursor();
                
                // Marquage des fantômes précédents de ce joueur comme périmés
                $req = $bdd->prepare("UPDATE fantomes SET etat = 'perime' WHERE etat = 'valide' AND uuidMap = :uuidMap AND (uuidJoueur = :uuidJoueur OR nomJoueur = :nomJoueur)");
                $req->execute(array(
                    'uuidMap' => $uuidMap,
                    'uuidJoueur' => $uuidJoueur,
                    'nomJoueur' => $d["nomJoueur"]
                )) or die("SQL error 39");
                $req->closeCursor();
            } else if ($_POST["action"] == "triche") {
                // Récupération de l'UUID de la map et du joueur pour ce fantôme
                $reponse = $bdd->prepare("SELECT f.uuidJoueur, f.nomJoueur, f.fichierFantome, s.operateurs FROM fantomes f INNER JOIN serveurs s ON f.idServ = s.id WHERE f.id = :id");
                $reponse->execute(array(
                    "id" => $idFantome
                )) or die("SQL error 58");
                $d = $reponse->fetch();
                $uuidJoueur = $d["uuidJoueur"];
                $reponse->closeCursor();
                
                // Marquage des fantômes précédents de ce joueur comme invalides si ce n'est pas un opérateur
                if (strpos($d["operateurs"], $d["uuidJoueur"]) === false) {
                    $req = $bdd->prepare("UPDATE fantomes SET etat = 'invalide' WHERE etat = 'valide' AND (uuidJoueur = :uuidJoueur OR nomJoueur = :nomJoueur)");
                    $req->execute(array(
                        'uuidJoueur' => $uuidJoueur,
                        'nomJoueur' => $d["nomJoueur"]
                    )) or die("SQL error 69");
                    $req->closeCursor();
                }
            }
            
            // Ajout du fantôme à la requête finale
            if ($i > 0)
                $strReq .= " OR";
            $strReq .= " id = :id" . $i;
            $arrayReq["id" . $i] = $idFantome;
            
            $i ++;
        }
        
        $req = $bdd->prepare($strReq);
        $req->execute($arrayReq) or die("SQL error 116");
        $req->closeCursor();
        
        header("Location: fantomes.php");
        die();
    }
}
?>
<section class="texte">
	<h1>Validation des fantômes</h1>
	<p>
		Il y a <?php echo htmlspecialchars($nbFantomesAFaire); ?> fantôme(s) à valider.<br />
		Le téléchargement des fantômes à valider sur le serveur de test est <?php echo $validationActivee ? '<span style="color:green">activé</span>' : '<span style="color:red">désactivé</span>'; ?>.
		<!-- (<a
			href="/user/admin!/fantomes.php?changerActivation=1">inverser</a>)-->
		<br /> <a href="user/admin!/fantomes.php?triche=1">Lancer une
			vérification de la triche</a>
	</p>
	<form action="user/admin!/fantomes.php" method="POST">
		<p>
			<select name="action">
				<option value="valider">Valider</option>
				<option value="pas valider">Pas valider</option>
				<option value="triche">Triche avérée</option>
			</select>&nbsp;&nbsp;<input type="submit" name="ok"
				value="Allons-y gaiement" /> <br /> 
	<?php
$reponse = $bdd->prepare("
			SELECT f.uuidJoueur uuidJoueur, f.nomJoueur nomFantome, f.ticks  AS ticksR, f.ticks * 0.05 AS ticks, f.id idFantome, DATE_FORMAT(f.date, '%d/%m à %Hh%i') dateFantome, f.millisecondes / 1000 AS millisec, f.rapportTriche rapportTriche, f.selecteur selecteur, m.id idMap, m.nom nomMap, m.uuid uuidMap
			FROM fantomes f
			INNER JOIN maps m ON f.uuidMap = m.uuid
			WHERE f.etat = 'attente'
			ORDER BY f.uuidMap, f.ticks ASC
			");
$reponse->execute(array()) or die("SQL error 83");
while ($d = $reponse->fetch()) {
    $donneesF[] = $d;
}
$reponse->closeCursor();
foreach ($donneesF as $d) {
    // Recherche du pourcentage de validité des fantômes de cet utilisateur
    $reponse = $bdd->prepare("SELECT COUNT(*) as nb FROM fantomes WHERE uuidJoueur = :uuidJoueur AND (etat = 'valide' OR etat = 'invalide')");
    $reponse->execute(array(
        "uuidJoueur" => $d["uuidJoueur"]
    )) or die("SQL error 93");
    $total = $reponse->fetch()["nb"];
    $reponse->closeCursor();
    $reponse = $bdd->prepare("SELECT COUNT(*) as nb FROM fantomes WHERE uuidJoueur = :uuidJoueur AND etat = 'valide'");
    $reponse->execute(array(
        "uuidJoueur" => $d["uuidJoueur"]
    )) or die("SQL error 99");
    $valides = $reponse->fetch()["nb"];
    $reponse->closeCursor();
    $pourcentage = round($valides / $total * 100);
    
    // Recherche de la place du joueur dans la map
    $reponse = $bdd->prepare("SELECT COUNT(*) as nb FROM fantomes WHERE uuidJoueur <> :uuidJoueur AND ticks < :ticks AND etat = 'valide' AND uuidMap = :uuidMap");
    $reponse->execute(array(
        "uuidJoueur" => $d["uuidJoueur"],
        "ticks" => $d["ticksR"],
        "uuidMap" => $d["uuidMap"]
    )) or die("SQL error 110");
    $rang = $reponse->fetch()["nb"] + 1;
    $reponse->closeCursor();
    // Recherche du nombre total de fantômes dans la map
    $reponse = $bdd->prepare("SELECT COUNT(*) as nb FROM fantomes WHERE etat = 'valide' AND uuidMap = :uuidMap");
    $reponse->execute(array(
        "uuidMap" => $d["uuidMap"]
    )) or die("SQL error 151");
    $totalFantomes = $reponse->fetch()["nb"] + 1;
    $reponse->closeCursor();
    
    // Affichage de la ligne
    $id = htmlspecialchars($d["idFantome"]);
    $ticks = htmlspecialchars(round($d["ticks"], 3));
    $milli = htmlspecialchars(round($d["millisec"], 3));
    // Si plus de 2 secondes de différence, en rouge
    if (abs($milli - $ticks > 2)) {
        $ticks = '<span style="color:red">' . $ticks;
        $milli .= '</span>';
    }
    $e = $rang == 1 ? "er" : "e";
    echo '<input type="checkbox" name="sélection[]" value="' . $id . '" id="f' . $id . '"> 
			<label for="f' . $id . '">' . htmlspecialchars($d["nomFantome"]) . ' (' . $ticks . '/' . $milli . ', ' . htmlspecialchars($rang) . '<sup>' . htmlspecialchars($e) . '</sup>/' . htmlspecialchars($totalFantomes) . ') 
					dans "<a href="map.php?id=' . htmlspecialchars($d["idMap"]) . '" target="_blank">' . htmlspecialchars($d["nomMap"]) . '</a>" le ' . htmlspecialchars($d["dateFantome"]) . '; val<sup>té</sup> : ' . htmlspecialchars($pourcentage . ' % ; ' . $d["selecteur"]) . '</label><br />';
    if ($d["rapportTriche"] != "ok") {
        if (! $d["rapportTriche"]) {
            echo '<span class="ligneInfosListe"><em>Triche non vérifiée</em></span><br />';
        } else {
            $explode = explode(";", $d["rapportTriche"]);
            foreach ($explode as $elem) {
                $explode2 = explode(":", $elem);
                $rapport .= $explode2[0] * 0.05 . ":" . $explode2[1] . "; ";
            }
            $rapport = rtrim($rapport, "; ");
            echo '<span class="ligneInfosListe"><strong>Triche détectée</strong> : ' . htmlspecialchars($rapport) . '</span><br />';
            unset($rapport);
        }
    }
}
?>
		</p>
	</form>
</section>
<?php include_once '../../includes/bas.php'; ?>