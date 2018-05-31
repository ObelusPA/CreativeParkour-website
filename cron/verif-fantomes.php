<?php
// ini_set ( 'display_errors', 1 );
// ini_set ( 'display_startup_errors', 1 );
// error_reporting ( E_ALL );
require_once '/home/creativeeb/www/includes/haut-global.php';

// Variables
define("WALKSPEED", 0.2);
define("XZ_SPEED_MAX", 0.25);
define("XZ_SPEED_MAX_SPRINT", 0.65);
define("XZ_SPEED_MAX_SNEAK", 0.15);
define("XZ_SPEED_MARGE", 0.0);
define("Y_MAX", 1.26);

// Récupération des fantômes à vérifier
$reponse = $bdd->prepare("SELECT id, uuidMap, uuidJoueur, fichierFantome, ticks, millisecondes FROM fantomes WHERE etat = 'attente' AND rapportTriche = ''");
$reponse->execute(array()) or die("SQL error 132");
$fantomes = $reponse->fetchAll();
$reponse->closeCursor();

foreach ($fantomes as $fantome) {
    // Chargement des données de la map
    $map = $maps[$fantome["uuidMap"]];
    if (! $map) {
        $map = new Map($fantome["uuidMap"]);
        $maps[$fantome["uuidMap"]] = $map;
    }
    
    // Lecture du fichier
    $zp = gzopen("/home/creativeeb/www/maps data/ghosts/" . $fantome["fichierFantome"] . ".gz", 'r');
    if (! is_bool($zp)) {
        while (! gzeof($zp)) {
            $buffer .= gzread($zp, 4096);
        }
        gzclose($zp);
        $positions = json_decode($buffer)->positions;
        unset($zp);
        unset($buffer);
        foreach ($positions as $elem) {
            $tickPreced = $tick;
            $posPreced = $pos;
            $tick = $elem->tick;
            $pos = new Position($elem->pos);
            $deltaTicks = $tick - $tickPreced;
            if ($tickPreced && $posPreced && $deltaTicks > 1) { // On ne le fait que s'il y a au moins 2 ticks d'écart, sinon ça fait chier.
                                                                // $dist = sqrt ( ($pos->x - $posPreced->x) ^ 2 + ($pos->y - $posPreced->y) ^ 2 + ($pos->z - $posPreced->z) ^ 2 );
                                                                // $temps = ($tick - $tickPreced) * 0.05;
                                                                // $vitesse = $dist / $temps;
                
                $x = $pos->x;
                $y = $pos->y;
                $z = $pos->z;
                
                if ($supprimerPerleAuProchainCoup) {
                    $supprimerPerleAuProchainCoup = false;
                    $enderpearl = false;
                }
                
                // Mise à jour des effets
                $speciaux = $map->getSpeciaux($x, $y, $z);
                if ($speciaux) {
                    foreach ($speciaux as $special) {
                        if ($special->t === "effects") {
                            // Le tableau des effets contient en clés leurs noms et en valeur leur amplifier (-1 si désactivé)
                            if ($special->duration <= 0)
                                $effets[$special->effect] = - 1;
                            else
                                $effets[$special->effect] = (int) $special->amplifier;
                        } else if ($special->t === "gives") {
                            if ($special->type === "ENDER_PEARL") {
                                if ($special->action === "give")
                                    $enderpearl = true;
                                else
                                    $supprimerPerleAuProchainCoup = true;
                            }
                        }
                    }
                }
                
                if (! $map->estCheckpoint($x, $y, $z) && ! $map->estTP($posPreced->x, $posPreced->y, $posPreced->z)) {
                    // Vérification du déplacement X/Z
                    $bloc = $map->getBloc($x, $y, $z);
                    if ($bloc != "SOUL_SAND" && strpos($bloc, "ICE") === false && ! $pos->elytres && ! $enderpearl) {
                        $deltaX = abs($x - $posPreced->x);
                        $deltaZ = abs($z - $posPreced->z);
                        if ($deltaX > 0 || $deltaZ > 0) {
                            $max = XZ_SPEED_MAX_SPRINT;
                            if (isset($effets["BLINDNESS"]) && $effets["BLINDNESS"] >= 0) {
                                $max = XZ_SPEED_MAX; // Pas de sprint
                            }
                            if (isset($effets["SPEED"]) && $effets["SPEED"] >= 0) {
                                $max += $max * (20 / 100) * ($effets["SPEED"] + 1); // TODO C'est de la merde
                                                                                        // echo $deltaX . "/" . $deltaZ . " (" . ($max + WALKSPEED * $deltaTicks) . ")<br />";
                            }
                            $max += WALKSPEED;
                            $max += XZ_SPEED_MARGE;
                            $max *= $deltaTicks;
                            if ($deltaX > $max || $deltaZ > $max) {
                                $problemes[$tick] .= "vitesse,";
                            }
                        }
                    }
                }
                
                // Vérification du déplacement Y
                $yPreced = $posPreced->y;
                $blocsSaut[] = $bloc;
                $blocDessous = $map->getBloc($x, $y - 1, $z);
                if ($y > $yPreced && abs($y - $yPreced) <= 0.1) { // Quand la trajectoire est proche de l'horizontale, on enregistre le bloc sous le joueur, c'est utile pour des escaliers de blocs entiers s'il n'y a pas d'endroit où la trajectoire redescent entre 2 blocs
                    $blocsSautDessous[] = $map->getBloc($x, $y - 1.5, $z);
                }
                if ($y <= $yPreced || strpos($blocDessous, "STEP") !== false || strpos($blocDessous, "STAIRS") !== false || (isset($effets["JUMP"]) && $effets["JUMP"] >= 0)) { // Si le joueur descend, on réinitialise
                    $posHaute = $posPreced;
                    // echo $posBasse . " && " . $posHaute->y . " > " . $posBasse->y . "<br />";
                    if ($posBasse && $posHaute->y > $posBasse->y) { // Si on a les 2 valeurs, on lance la vérification
                        $blocDessous = $map->getBloc($posBasse->x, $posBasse->y - 1, $posBasse->z);
                        $blocDessous2 = $map->getBloc($posBasse->x, $posBasse->y - 1.5, $posBasse->z);
                        if ($blocDessous != "SLIME_BLOCK" && $blocDessous2 != "SLIME_BLOCK" && ! in_array("VINE", $blocsSaut) && ! in_array("LADDER", $blocsSaut) && in_array("AIR", $blocsSautDessous) && ! $map->estCheckpoint($posHaute->x, $posHaute->y, $posHaute->z)) {
                            $deltaY = $posHaute->y - $posBasse->y;
                            // echo $deltaY . " (" . ($tick * 0.05) . ")<br />";
                            if ($deltaY > Y_MAX) {
                                $problemes[$tick] .= "saut,";
                            }
                        }
                    }
                    $posBasse = null;
                } else if ($posBasse === null) {
                    $posBasse = $posPreced;
                    $blocsSaut = array();
                    $blocsSautDessous = array();
                }
                
                if ($problemes[$tick]) {
                    $problemes[$tick] = rtrim($problemes[$tick], ",");
                }
            }
        }
        
        $rapport = "ok";
        if ($problemes) {
            $rapport = "";
            $ecartMin = 999;
            $tPreced = 0;
            foreach ($problemes as $t => $pb) {
                $rapport .= htmlspecialchars($t . ":" . $pb . ";");
                $ecartMin = min($ecartMin, $t - $tPreced);
            }
            $rapport = rtrim($rapport, ";");
            echo "Fantôme " . htmlspecialchars($fantome["id"]) . " positif (map " . $fantome["uuidMap"] . ")<br />";
            echo $rapport;
            echo "<hr />";
        }
        
        // Validation ou dévalidation automatique sous certaines conditions
        $etat = "attente";
        // Vérification de l'écart avec le temps réel (par minute)
        $ecart = $fantome["millisecondes"] - $fantome["ticks"] * 50;
        $multiplicateur = $fantome["millisecondes"] / 1000 / 60;
        // Recherche de l'état de la map (on invalide le fantôme si elle est supprimée)
        $reponse = $bdd->prepare("SELECT etat FROM maps WHERE uuid = :uuid");
        $reponse->execute(array(
            "uuid" => $map->uuid
        )) or die("SQL error 163");
        $etatMap = $reponse->fetch()["etat"];
        $reponse->closeCursor();
        if ($etatMap == "supprimee" || $ecart > 6000 * $multiplicateur) {
            $etat = "invalide";
        } else {
            // Recherche du rang du mec
            $reponse = $bdd->prepare("SELECT (SELECT COUNT(*) FROM fantomes WHERE uuidJoueur <> :uuidJoueur AND ticks < :ticks AND etat = 'valide' AND uuidMap = :uuidMap) as nb, (SELECT COUNT(*) FROM fantomes WHERE uuidJoueur <> :uuidJoueur AND ticks < :ticks AND (etat = 'valide' OR etat = 'attente') AND uuidMap = :uuidMap) as nbA FROM fantomes");
            $reponse->execute(array(
                "uuidJoueur" => $fantome["uuidJoueur"],
                "ticks" => $fantome["ticks"],
                "uuidMap" => $fantome["uuidMap"]
            )) or die("SQL error 181");
            $r = $reponse->fetch();
            $rang = $r["nb"] + 1;
            $rangAttente = $r["nbA"] + 1;
            $reponse->closeCursor();
            if ($rang > 3) {
                // Plus d'indulgence si mauvais rang
                if ($ecart <= 500 * $multiplicateur || ($rang > 15 && $ecart <= 1200 * $multiplicateur) || ($rang > 40 && $ecart <= 3000 * $multiplicateur) || ($rang > 100 && $ecart <= 5000 * $multiplicateur)) {
                    // Recherche du pourcentage de triche du gars
                    $reponse = $bdd->prepare("SELECT (SELECT COUNT(*) FROM fantomes WHERE uuidJoueur = :uuidJoueur) as total, (SELECT COUNT(*) FROM fantomes WHERE uuidJoueur = :uuidJoueur AND triche = 1) as triche FROM fantomes");
                    $reponse->execute(array(
                        "uuidJoueur" => $fantome["uuidJoueur"]
                    )) or die("SQL error 188");
                    $d = $reponse->fetch();
                    $reponse->closeCursor();
                    
                    // Si moins de 10 % de triche
                    if ($d["triche"] / $d["total"] < 0.1) {
                        if (count($problemes) <= 1 || $ecartMin >= 10 || ($rang > 30 && ($ecartMin >= 3 || count($problemes) <= 5))) {
                            $etat = "valide";
                        }
                    }
                }
            } else if ($rangAttente > 3) { // S'il est plus de troisième en comptant ceux en attente, on ne fait rien, on s'en occupera quand ceux d'avant auront été faits
                $pasVerifier = true;
            }
        }
        
        // Mise à jour du fantôme
        if (! $pasVerifier) {
            $req = $bdd->prepare("UPDATE fantomes SET etat = :etat, rapportTriche = :rapport, selecteur = :selecteur WHERE id = :id");
            $req->execute(array(
                "id" => $fantome["id"],
                "etat" => $etat,
                "rapport" => $rapport,
                "selecteur" => selecteurFantome($fantome["id"])
            )) or erreurAPI("SQL error 201");
            $req->closeCursor();
        }
        
        unset($tick);
        unset($pos);
        unset($effets);
        unset($problemes);
        unset($blocsSaut);
        unset($blocsSautDessous);
        unset($posBasse);
        unset($posHaute);
        unset($pasVerifier);
    }
}
?>