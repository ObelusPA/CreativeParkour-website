<?php

/**
 * Affiche la belle page d'erreur avec le message donné <strong>qui doit déjà avoir été filtré par htmlspecialchars</strong>.
 * @param unknown $message
 */
function pageErreur($message)
{
    $messageErreur = $message;
    require_once 'erreur.php';
}

/**
 * Vérification des bannissements (IP et éventuel UUID du joueur)
 *
 * @param string $retourner
 *            Si true, le script n'est pas interrompu et true est retourné si le mec est banni
 * @param string $ip
 *            IP à vérifier
 * @param string $joueurUUID
 *            UUID de joueur à vérifier
 * @return boolean true si le mec est banni
 */
function verifBan($retourner = false, $ip = null, $joueurUUID = null)
{
    global $bdd;
    $reponse = $bdd->prepare("SELECT raison FROM bans WHERE (ip = :ip OR uuidJoueur = :uuidJoueur) AND dateFin > NOW() ORDER BY dateFin DESC LIMIT 1");
    $reponse->execute(array(
        'ip' => $ip ? $ip : getUserIP(),
        'uuidJoueur' => $joueurUUID
    )) or die("SQL error 13");
    $donnees = $reponse->fetch();
    if ($donnees) {
        if (! $retourner) {
            http_response_code(403);
            die("You are not allowed to access this website.<br />Reason : " . htmlspecialchars(lcfirst($donnees["raison"])));
        }
        return true;
    }
    $reponse->closeCursor();
    return false;
}

/**
 * Banni quelqu'un en fonction des paramètres
 */
function bannir($dateFin, $raison, $ip = "", $uuidJoueur = "", $idServeur = 0)
{
    global $bdd;
    if ($dateFin === 'infini')
        $dateFin = "9999-01-01 00:00:00";
    else
        $dateFin = date('Y-m-d H:i:s', $dateFin);
    $req = $bdd->prepare("INSERT INTO bans SET date = NOW(), dateFin = :dateFin, ip = :ip, uuidJoueur = :uuidJ, idServeur = :idServ, raison = :raison");
    $req->execute(array(
        'dateFin' => $dateFin,
        'ip' => $ip,
        'uuidJ' => $uuidJoueur,
        'idServ' => $idServeur,
        'raison' => $raison
    )) or die("SQL error 37");
    $req->closeCursor();
}

function getUserIP()
{
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];
    
    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }
    
    return $ip;
}

/**
 * Retourne l'index du serveur ayant la clé en paramètre dans la table des serveurs
 *
 * @param unknown $cle
 *            Clé du serveur
 */
function getServIDAvecUUID($uuid)
{
    global $bdd;
    $reponse = $bdd->prepare("SELECT id FROM serveurs WHERE uuid = :uuid");
    $reponse->execute(array(
        'uuid' => $uuid
    )) or die("SQL error 67");
    $donnees = $reponse->fetch();
    if ($donnees) {
        return $donnees["id"];
    }
    $reponse->closeCursor();
    return 0;
}

/**
 *
 * @param String $nom
 *            Nom du provider auquel la colonne retournée doit correspondre
 * @return String Nom de la colonne correspondant au provider donné
 */
function getProviderCol($nom)
{
    if (strtolower($nom) === "facebook")
        return "facebookID";
    elseif (strtolower($nom) === "twitter")
        return "twitterID";
    elseif (strtolower($nom) === "google")
        return "googleID";
    elseif (strtolower($nom) === "discord")
        return "discordID";
    return null;
}

/**
 * Retourne un utilisateur avec le mail ou nom et mot de passe
 *
 * @param String $mailOuNom
 *            Mail ou nom pour se connecter
 * @param Stirng $mdp
 *            Mot de passe entré
 * @return Utilisateur|NULL Utilisateur s'il est trouvé ou NULL sinon
 */
function getUtilisateurLocal($mailOuNom, $mdp)
{
    global $bdd;
    $reponse = $bdd->prepare("SELECT id, mdp AS hash, nom, facebookID, twitterID, googleID, discordID, minecraftUUID FROM utilisateurs WHERE id= :idMail OR nom = :mailOuNom");
    $reponse->execute(array(
        'idMail' => getUserID($mailOuNom, true),
        'mailOuNom' => $mailOuNom
    )) or die("SQL error 126");
    $donnees = $reponse->fetch();
    $reponse->closeCursor();
    if ($donnees && password_verify($mdp, $donnees['hash'])) {
        return new Utilisateur($donnees['id'], getMail($donnees['id']), $donnees['nom'], $donnees['minecraftUUID'], $donnees['facebookID'], $donnees['twitterID'], $donnees['googleID'], $donnees['discordID']);
    }
    return null;
}

/**
 * Retourne un utilisateur qui s'est connecté avec un réseau social
 *
 * @param String $provider
 *            Nom du réseau social (Facebook, Twitter, Google ou Discord)
 * @param unknown $userID
 *            ID de l'utilisateur sur le réseau social
 * @return Utilisateur|NULL Utilisateur s'il est trouvé ou NULL sinon ou si le nom du réseau sociel est erroné
 */
function getUtilisateurSocial($provider, $userID)
{
    global $bdd;
    $providerSQL = getProviderCol($provider);
    
    if (! isset($providerSQL))
        return null;
    else {
        $reponse = $bdd->prepare("SELECT id, nom, facebookID, twitterID, googleID, discordID, minecraftUUID FROM utilisateurs WHERE " . $providerSQL . " = :userID");
        $reponse->execute(array(
            'userID' => $userID
        )) or die("SQL error 153");
        $donnees = $reponse->fetch();
        $reponse->closeCursor();
        if ($donnees) {
            return new Utilisateur($donnees['id'], getMail($donnees['id']), $donnees['nom'], $donnees['minecraftUUID'], $donnees['facebookID'], $donnees['twitterID'], $donnees['googleID'], $donnees['discordID']);
        }
    }
    return null;
}

function getMail($idUtilisateur)
{
    global $bdd;
    $reponse = $bdd->prepare("SELECT adresse FROM mails WHERE idUtilisateur = :id AND principale = 1 ORDER BY id DESC LIMIT 1");
    $reponse->execute(array(
        'id' => $idUtilisateur
    )) or die("SQL error 167");
    $adresse = $reponse->fetch()["adresse"];
    $reponse->closeCursor();
    return $adresse;
}

/**
 * Récupère l'ID de l'utilisateur en fonction de son mail
 *
 * @param unknown $mail
 * @param unknown $uniquementPrincipale
 *            true si on ne veut des résultats que si c'est l'adresse principale de l'utilisateur
 * @return mixed
 */
function getUserID($mail, $uniquementPrincipale)
{
    global $bdd;
    $requete = "SELECT idUtilisateur FROM mails WHERE lower(adresse) = lower(:mail) ORDER BY id DESC LIMIT 1";
    if ($uniquementPrincipale)
        $requete = "SELECT idUtilisateur FROM mails WHERE lower(adresse) = lower(:mail) AND principale = 1 ORDER BY id DESC LIMIT 1";
    $reponse = $bdd->prepare($requete);
    $reponse->execute(array(
        'mail' => $mail
    )) or die("SQL error 174");
    $id = $reponse->fetch()["idUtilisateur"];
    $reponse->closeCursor();
    return $id;
}

/**
 * Crée une nouvelle clé temporaire
 *
 * @param String $ipJoueur
 *            IP de l'utilisateur qui va accéder au site avec la clé
 * @return unknown[] id de la clé insérée ("id") et la clé ("clé")
 */
function nouvelleCle($ipJoueur, $uuidJoueur, $nomJoueur = null)
{
    global $bdd;
    // Suppression des clés déjà existantes pour cette IP
    $req = $bdd->prepare("DELETE FROM cles WHERE (ipJoueur = :ipJoueur OR uuidJoueur = :uuidJoueur)");
    $req->execute(array(
        'ipJoueur' => $ipJoueur,
        'uuidJoueur' => $uuidJoueur
    )) or die("SQL error 208");
    $req->closeCursor();
    
    // Ajout de la nouvelle clé
    $cle = genererJeton(32);
    $req = $bdd->prepare("INSERT INTO cles SET cle = :cle, expiration = DATE_ADD(NOW(), INTERVAL 10 MINUTE), ipJoueur = :ipJoueur, uuidJoueur = :uuidJoueur");
    $req->execute(array(
        'cle' => password_hash($cle, PASSWORD_DEFAULT),
        'ipJoueur' => $ipJoueur,
        'uuidJoueur' => $uuidJoueur
    )) or die("SQL error 218");
    $id = $bdd->lastInsertId();
    $req->closeCursor();
    
    // Enregistrement du nom du joueur s'il ne l'est pas déjà
    if ($nomJoueur) {
        $reponse = $bdd->prepare("SELECT nom FROM joueursMC WHERE uuid = :uuid");
        $reponse->execute(array(
            "uuid" => $uuidJoueur
        )) or die("SQL error 233");
        $donnees = $reponse->fetch();
        $reponse->closeCursor();
        if (! $donnees["nom"]) {
            enregistrerNomMC($uuidJoueur, $nomJoueur);
        }
    }
    
    return new Cle($id, $cle, $ipJoueur, $uuidJoueur);
}

/**
 * Retourne true si l'utilisateur sest connecté, false sinon
 */
function connecte()
{
    return isset($_SESSION["utilisateur"]);
}

/**
 * Renvoie l'utilisateur sur la page de connexion s'il n'est pas connecté
 */
function verifLogin($message = null)
{
    if (! connecte()) {
        $_SESSION["return"] = $_SERVER['REQUEST_URI'];
        if ($message)
            $_SESSION["msgOK"][] = $message;
        else
            $_SESSION["erreurs"][] = "For security reasons, you must be logged in to access this page.";
        header("Location: login.php");
        die();
    }
}

/**
 * Redirige l'utilisateur à l'endroit indiqué dans $_SESSION ["return"] et supprime cette variable ou alors à l'endroit passé en argument
 */
function retournerOuHeader($loc)
{
    if ($_SESSION["return"]) {
        header("Location: .." . $_SESSION["return"]);
        unset($_SESSION["return"]);
    } else
        header("Location: " . $loc);
    die("Redirecting...");
}

function boutonForm($idForm, $value, $captcha = false)
{
    $callback = 'onSubmit' . genererJeton(4);
    echo '<input type="submit" ';
    if ($captcha)
        echo 'class="g-recaptcha" data-sitekey="6Lc7ARkUAAAAAPCUAnIQ0KP_00oxoa46G8LwJuym" ';
    echo 'data-callback="' . htmlspecialchars($callback) . '" value="' . htmlspecialchars($value) . '">';
    echo '<script>
       function ' . htmlspecialchars($callback) . '(token) {
         document.getElementById("' . htmlspecialchars($idForm) . '").submit();
       }
     </script>';
}

function verifReCaptcha()
{   
    $post_data = http_build_query(
        array(
            'secret' => '############',
            'response' => $_POST['g-recaptcha-response'],
            'remoteip' => $_SERVER['REMOTE_ADDR']
        )
        );
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $post_data
        )
    );
    $context  = stream_context_create($opts);
    $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
    $result = json_decode($response);
    return $result->success;
        
}

function getProfilMC($uuid)
{
    global $bdd;
    // Recherche dans la base de données
    $reponse = $bdd->prepare("SELECT nom, textures, signature FROM joueursMC WHERE uuid = :uuid");
    $reponse->execute(array(
        "uuid" => $uuid
    )) or die("SQL error 280");
    $donnees = $reponse->fetch();
    $reponse->closeCursor();
    if ($donnees["nom"]) {
        return array(
            "nom" => $donnees["nom"],
            "textures" => $donnees["textures"],
            "signature" => $donnees["signature"]
        );
    } else {
        $profil = telechargerProfilMC($uuid);
        enregistrerProfilMC($uuid, $profil);
        return $profil;
    }
}

function getNomMC($uuid)
{
    return getProfilMC($uuid)["nom"];
}

function telechargerNomMC($uuid)
{
    preg_match("#\"name\":\"(.+)\"#U", file_get_contents("https://sessionserver.mojang.com/session/minecraft/profile/" . str_replace("-", "", $uuid)), $matches);
    return $matches[1];
}

function telechargerProfilMC($uuid)
{
    $json = json_decode(file_get_contents("https://sessionserver.mojang.com/session/minecraft/profile/" . str_replace("-", "", $uuid) . "?unsigned=false"));
    $a["nom"] = $json->name;
    $properties = $json->properties;
    if (is_array($properties)) {
        foreach ($properties as $p) {
            if ($p->name == "textures") {
                $a["textures"] = $p->value;
                $a["signature"] = $p->signature;
            }
        }
    }
    return $a;
}

function enregistrerProfilMC($uuid, $profil)
{
    global $bdd;
    // Recherche de si ne nom existe déjà pour savoir quelle requête faire
    $reponse = $bdd->prepare("SELECT nom FROM joueursMC WHERE uuid = :uuid");
    $reponse->execute(array(
        "uuid" => $uuid
    )) or die("SQL error 296");
    $donnees = $reponse->fetch();
    $reponse->closeCursor();
    if ($donnees["nom"]) {
        $requete = "UPDATE joueursMC SET nom = :nom, dateMAJ = NOW(), textures = :textures, signature = :signature WHERE uuid = :uuid";
    } else {
        $requete = "INSERT INTO joueursMC SET uuid = :uuid, nom = :nom, dateMAJ = NOW(), textures = :textures, signature = :signature";
    }
    
    // Mise à jour de la table
    $req = $bdd->prepare($requete);
    $req->execute(array(
        'uuid' => $uuid,
        'nom' => $profil["nom"],
        'textures' => $profil["textures"],
        'signature' => $profil["signature"]
    )) or die("SQL error 351");
    $req->closeCursor();
}

function enregistrerNomMC($uuid, $nom)
{
    global $bdd;
    // Recherche de si ne nom existe déjà pour savoir quelle requête faire
    $reponse = $bdd->prepare("SELECT nom FROM joueursMC WHERE uuid = :uuid");
    $reponse->execute(array(
        "uuid" => $uuid
    )) or die("SQL error 296");
    $donnees = $reponse->fetch();
    $reponse->closeCursor();
    if ($donnees["nom"]) {
        $requete = "UPDATE joueursMC SET nom = :nom, dateMAJ = NOW() WHERE uuid = :uuid";
    } else {
        $requete = "INSERT INTO joueursMC SET uuid = :uuid, nom = :nom, dateMAJ = NOW()";
    }
    
    // Mise à jour de la table
    $req = $bdd->prepare($requete);
    $req->execute(array(
        'uuid' => $uuid,
        'nom' => $nom
    )) or die("SQL error 374");
    $req->closeCursor();
}

/**
 * Remplace null par une chaîne de caractères vide si $var l'est
 *
 * @param unknown $var
 * @return string|unknown
 */
function nullVide($var)
{
    return $var === null ? "" : $var;
}

function genererJeton($taille)
{
    return bin2hex(random_bytes($taille / 2));
}

function envoyerMail($destinataire, $titre, $message, $reply = null)
{
    global $bdd;
    $headers = "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/html; charset=utf-8\n";
    $headers .= "From: \"CreativeParkour\"<############>\n";
    if ($reply)
        $headers .= "Reply-To: " . htmlspecialchars($reply) . "\n";
    @mail($destinataire, $titre, $message, $headers, '-f############');
    
    // Ajout de l'envoi au comptage
    $req = $bdd->prepare("UPDATE mails SET nbEnvois = 1 + nbEnvois WHERE adresse = :mail");
    $req->execute(array(
        'mail' => $destinataire
    )) or die("SQL error 397");
    $req->closeCursor();
}

/**
 * Retourne le morceau d'un mail générique
 */
function partieMail($partie)
{
    if ($partie === "haut")
        return '<html><body><div style="text-align: center; padding: 5px; background-color: #ffc005; font-family: \'AR JULIAN\', Trebuchet MS, Helvetica, Arial, sans-serif; font-size: 4.5em"><span style="color: #fef79c">[</span><span style="color: white">CreativeParkour</span><span style="color: #fef79c">]</span></div>';
    if ($partie === "bas")
        return '</p></body></html>';
}

/**
 * Enregistre l'adresse mail donnée si elle ne l'est pas déjà et envoie le mail de vérification
 *
 * @param int $idUtilisateur
 * @param string $adresse
 *            Mail
 * @param string $pseudo
 *            Pseudo du mec pour le mail
 * @param boolean $principale
 *            true s'il faut définir tout de suite cette adresse comme la principale (sinon elle le sera au moment de sa validation)
 * @param boolean $verifier
 *            false pour marquer l'adresse comme vérifiée
 */
function ajouterMailEtVerifier($idUtilisateur, $adresse, $pseudo, $principale, $verifier = true)
{
    global $bdd;
    $reponse = $bdd->prepare("SELECT id, verif FROM mails WHERE lower(adresse) = lower(:mail) ORDER BY id DESC LIMIT 1");
    $reponse->execute(array(
        "mail" => $adresse
    )) or die("SQL error 405");
    $donnees = $reponse->fetch();
    $reponse->closeCursor();
    
    $jeton = genererJeton(64);
    // Préparation de la requête pour mettre la table à jour
    $requete = "INSERT INTO mails SET idUtilisateur = :idU, adresse = :mail, dateAjout = NOW(), verif = :verif, principale = :principale";
    $args = array(
        'idU' => $idUtilisateur,
        'mail' => $adresse,
        'verif' => $verifier ? password_hash($jeton, PASSWORD_BCRYPT) : "",
        'principale' => $principale ? 1 : 0
    );
    if ($donnees && ! $donnees["verif"]) // Si on connaît déjà l'adresse et qu'elle est déjà validée, on ne fait rien
        return;
    else if ($donnees) { // Si on connaît déjà l'adresse mais qu'elle n'est pas validée, on régénère le jeton
        $requete = "UPDATE mails SET verif = :verif, principale = :principale WHERE id = :id";
        $args = array(
            'id' => $donnees["id"],
            'verif' => $verifier ? password_hash($jeton, PASSWORD_BCRYPT) : "",
            'principale' => $principale ? 1 : 0
        );
    }
    
    // Exécution de la requête
    $req = $bdd->prepare($requete);
    $req->execute($args) or die("SQL error 429");
    $req->closeCursor();
    
    if ($verifier) {
        // Envoi du mail
        $lien = "https://creativeparkour.net/user/email-check.php?token=" . htmlspecialchars($jeton);
        $message = partieMail("haut");
        $message .= '<h1>CreativeParkour.net email verification</h1>';
        $message .= '<p>Hello ' . htmlspecialchars($pseudo) . ', thank you for joining CreativeParkour.net! To complete your registration, please verify your email by clicking on the following link :<br />';
        $message .= '<a href="' . $lien . '">' . $lien . '</a><br /><br />';
        $message .= 'If you have any question or if you have not registered on this website, please reply to this email.<br />';
        $message .= 'Have a nice day!';
        $message .= partieMail("bas");
        envoyerMail($adresse, "CreativeParkour.net email verification", $message);
        $_SESSION["msgOK"][] = "An email has been sent to " . htmlspecialchars($adresse) . " to verify the address, please check it out.";
    }
}

function rememberMe()
{
    global $bdd;
    if ($_SESSION["utilisateur"]) {
        $selecteur = genererJeton(12); // Si les valeurs sont modifiées ici, il faut aussi les modifier en bas de haut-global.php
        $jeton = genererJeton(72);
        
        // Suppression des entrées pour cet utilisateur
        $req = $bdd->prepare("DELETE FROM jetonsConnexion WHERE idUtilisateur = :idUtilisateur");
        $req->execute(array(
            'idUtilisateur' => $_SESSION["utilisateur"]->id
        )) or die("SQL error 480");
        $req->closeCursor();
        
        // Mise à jour de la table
        $req = $bdd->prepare("INSERT INTO jetonsConnexion SET selecteur = :selecteur, jeton = :jeton, idUtilisateur = :idUtilisateur, expiration = DATE_ADD(NOW(), INTERVAL 60 DAY)");
        $req->execute(array(
            'selecteur' => $selecteur,
            'jeton' => password_hash($jeton, PASSWORD_BCRYPT),
            'idUtilisateur' => $_SESSION["utilisateur"]->id
        )) or die("SQL error 22");
        $req->closeCursor();
        
        setcookie("login", $selecteur . ":" . $jeton, time() + (86400 * 30), "/");
    }
}

function getExtensionFichier($str)
{
    $i = strrpos($str, ".");
    if (! $i) {
        return "";
    }
    $l = strlen($str) - $i;
    $ext = substr($str, $i + 1, $l);
    return $ext;
}

/**
 * Sépare le nom et l'UUID dans une chaîne de la forme uuid:nom
 *
 * @param string $uuidEtNom
 */
function separerUuidNom($uuidEtNom)
{
    $tab = explode(":", $uuidEtNom);
    $tab["uuid"] = $tab[0];
    $tab["nom"] = $tab[1];
    return $tab;
}

/**
 * Met à jour les notes de la map en la calculant à partir de tous les votes
 *
 * @param int $idMap
 */
function calculerNotes($idMap)
{
    global $bdd;
    // Calcul de la moyenne
    $reponse = $bdd->prepare("SELECT difficulte, qualite FROM votes WHERE idMap = :idMap AND etat = 'valide'");
    $reponse->execute(array(
        "idMap" => $idMap
    )) or die("SQL error 528");
    $sommeD = 0;
    $nombreD = 0;
    $sommeQ = 0;
    $nombreQ = 0;
    while ($donnees = $reponse->fetch()) {
        if ($donnees["difficulte"] > 0) {
            $sommeD += $donnees["difficulte"];
            $nombreD += 1;
        }
        if ($donnees["qualite"] > 0) {
            $sommeQ += $donnees["qualite"];
            $nombreQ += 1;
        }
    }
    $reponse->closeCursor();
    
    // Mise à jour de la valeur
    if ($nombreD > 0)
        $moyenneD = $sommeD / $nombreD;
    if (! $moyenneD)
        $moyenneD = - 1;
    if ($nombreQ > 0)
        $moyenneQ = $sommeQ / $nombreQ;
    if (! $moyenneQ)
        $moyenneQ = - 1;
    $req = $bdd->prepare("UPDATE maps SET difficulte = :difficulte, qualite = :qualite WHERE id = :idMap");
    $req->execute(array(
        'difficulte' => $moyenneD,
        'qualite' => $moyenneQ,
        'idMap' => $idMap
    )) or die("SQL error 558");
    $req->closeCursor();
}

/**
 * Met à jour le nombre de téléchargements de la map
 *
 * @param int $idMap
 */
function calculerTelechargements($idMap)
{
    global $bdd;
    // Somme
    $reponse = $bdd->prepare("SELECT COUNT(*) nombre FROM telechargements WHERE idMap = :idMap");
    $reponse->execute(array(
        "idMap" => $idMap
    )) or die("SQL error 574");
    $donnees = $reponse->fetch();
    $reponse->closeCursor();
    
    // Mise à jour de la valeur
    $req = $bdd->prepare("UPDATE maps SET nbTelechargements = :nbTelechargements WHERE id = :idMap");
    $req->execute(array(
        'nbTelechargements' => $donnees["nombre"],
        'idMap' => $idMap
    )) or die("SQL error 583");
    $req->closeCursor();
}

function tronquerZero($float)
{
    if (substr($float, - 2) == ".0")
        return str_replace(".0", "", $float);
    return $float;
}

function texteDifficulte($difficulte, $minuscule = false)
{
    $texte = 'Unknown difficulty';
    if ($minuscule)
        $texte = 'unknown difficulty';
    if ($difficulte > 0) {
        if ($difficulte < 1.5) {
            $texte = '<span class="very-easy">';
            $diff = "Very easy";
        }
        if ($difficulte >= 1.5) {
            $texte = '<span class="easy">';
            $diff = "Easy";
        }
        if ($difficulte >= 2.5) {
            $texte = '<span class="medium">';
            $diff = "Medium";
        }
        if ($difficulte >= 3.5) {
            $texte = '<span class="hard">';
            $diff = "Hard";
        }
        if ($difficulte >= 4.5) {
            $texte = '<span class="extreme">';
            $diff = "Extreme";
        }
        if ($minuscule)
            $diff = lcfirst($diff);
        $texte .= $diff;
        $texte .= ' (' . htmlspecialchars(tronquerZero($difficulte)) . '/5)</span>';
    }
    return $texte;
}

function etoiles($qualite, $texteRemplacement = false)
{
    if (! $qualite || $qualite <= 0) {
        if ($texteRemplacement)
            echo "unrated";
    } else {
        for (; round($qualite) >= 1; $qualite --) {
            echo '<img class="etoile" src="images/étoile.png" alt="Full star">';
            $nb ++;
        }
        if ($qualite > 0) {
            echo '<img class="etoile" src="images/étoile-demi.png" alt="Half star">';
            $nb ++;
        }
        for (; $nb < 5; $nb ++) {
            echo '<img class="etoile" src="images/étoile-grise.png" alt="Empty star">';
        }
    }
}

function texteVersion($difficulte)
{
    $c = "#2c00ad";
    if ($difficulte == 9)
        $c = "#0600fd";
    else if ($difficulte == 10)
        $c = "#8600fd";
    else if ($difficulte == 11)
        $c = "#c100fd";
    else if ($difficulte == 12)
        $c = "#e400fd";
    return '<span style="color: ' . htmlspecialchars($c) . '">1.' . htmlspecialchars($difficulte) . '</span>';
}

function texteConversion($versionMin, $verConversion)
{
    return 'This map contains 1.' . htmlspecialchars($versionMin) . ' blocks, but CreativeParkour can convert them to 1.' . htmlspecialchars($verConversion) . '-compatible blocks.';
}

function afficherLigneMap($donnees)
{
    $nomsCreateurs = nomTete(separerUuidNom($donnees["createurMap"])["uuid"]);
    if ($donnees["contributeursMap"]) {
        $contributeurs = explode(";", $donnees["contributeursMap"]);
        foreach ($contributeurs as $c) {
            $nomsCreateurs .= ", " . nomTete(separerUuidNom($c)["uuid"]);
        }
    }
    $texteVersion = texteVersion($donnees["verConversion"]);
    // Ajout de l'info de conversion si c'est le cas
    if ($donnees["versionMin"] > $donnees["verConversion"]) {
        $texteVersion = '<span class="texteInfo" title="' . texteConversion($donnees["versionMin"], $donnees["verConversion"]) . '">' . $texteVersion . '</span>';
    }
    ?>
<tr>
	<td class="image"><?php if ($donnees ["imageMap"]) { ?><a
		href="images/maps/<?php echo htmlspecialchars ( rawurlencode($donnees ["imageMap"] )); ?>"
		class="highslide" onclick="return hs.expand(this)"> <img
			src="images/maps/mini/<?php echo htmlspecialchars(rawurlencode($donnees["imageMap"])); ?>"
			alt="<?php echo htmlspecialchars($donnees["nomMap"]); ?> screenshot"
			title="Click to enlarge" /></a><?php } ?></td>

	<td class="description"><strong><a title="Click for details"
			href="map.php?id=<?php echo htmlspecialchars($donnees["idMap"]); ?>"><?php echo htmlspecialchars($donnees["nomMap"]); ?></a></strong> <?php etoiles($donnees["qualite"])?><br />
				<?php echo texteDifficulte($donnees ["difficulte"]); ?> - Minimum MC version: <?php echo $texteVersion; ?><br />
				By <?php echo $nomsCreateurs?><br />
				<?php
    if ($donnees["etatServ"] == "public") {
        echo 'Creation server: <em><a href="server.php?id=' . htmlspecialchars($donnees["idServ"]) . '">' . htmlspecialchars($donnees["nomServ"]) . '</a></em>';
    }
    ?><br /> <em class="indication-jouer">Type "/cpd <?php echo htmlspecialchars($donnees["idMap"]); ?>" on a server running CreativeParkour to play! <a
			href="doc/play.php">Help</a></em></td>
	<td class="details"><a title="Click for details"
		href="map.php?id=<?php echo htmlspecialchars($donnees["idMap"]); ?>"><img
			alt="Details" src="images/flèche.png"></a></td>
</tr>
<?php
}

function serveurValide($etat)
{
    return $etat == "public" || $etat == "prive";
}

function getIdServValide($uuid)
{
    return getIdServ($uuid, true);
}

function getIdServ($uuid, $doitEtreValide)
{
    global $bdd;
    $idServ = 0;
    $reponse = $bdd->prepare("SELECT id, etat FROM serveurs WHERE uuid = :uuidServ");
    $reponse->execute(array(
        "uuidServ" => $uuid
    )) or die("SQL error 505");
    $donnees = $reponse->fetch();
    
    if (! $doitEtreValide || serveurValide($donnees["etat"]))
        $idServ = $donnees["id"];
    $reponse->closeCursor();
    return $idServ;
}

function getIdMap($uuid)
{
    global $bdd;
    $idMap = 0;
    $reponse = $bdd->prepare("SELECT id FROM maps WHERE uuid = :uuid");
    $reponse->execute(array(
        "uuid" => $uuid
    )) or die("SQL error 688");
    $idMap = $reponse->fetch()["id"];
    $reponse->closeCursor();
    return $idMap;
}

function documentation($bas = false, $partage = false)
{
    if ($bas) {
        ?>
<em class="margeGauche">Something missing? A mistake? A suggestion? A
	question? <a href="contact.php">Contact me</a>!
</em>
<br />
<br />
<?php
    }
    if ($partage) {
        afficherBarrePartage(true);
    }
    ?>
<div class="navDocumentation">
	Documentation &rsaquo; <a href="doc/map-creation.php">Map&nbsp;creation</a>
	&middot; <a href="doc/commands.php">Commands</a> &middot; <a
		href="doc/permissions.php">Permissions</a> &middot; <a
		href="doc/configuration.php">Configuration</a> &middot; <a
		href="doc/add-map.php">Adding&nbsp;a&nbsp;map&nbsp;to&nbsp;the&nbsp;website</a>
	&middot; <a href="doc/rewards.php">Custom&nbsp;rewards</a> &middot; <a
		href="doc/lobby-signs.php">Lobby&nbsp;signs</a> &middot; <a
		href="doc/languages.php">Languages</a> &middot; <a href="doc/faq.php">FAQ</a>
	&middot; <a href="https://github.com/ObelusPA/CreativeParkour#api"
		target="_blank">API</a> &middot; <a href="javadoc">Javadoc</a>
</div>
<?php
}

function ecrireLog($texte)
{
    global $bdd;
    $req = $bdd->prepare("INSERT INTO log SET date = NOW(), texte = :texte");
    $req->execute(array(
        "texte" => print_r($texte, true)
    )) or die("SQL error 541");
    $req->closeCursor();
}

function imageFlottanteDoc($image, $largeur, $opti = null, $clear = false)
{
    $clearCSS = $clear ? "clear:right;" : "";
    echo '<a style="width:' . htmlspecialchars($largeur) . 'px;' . htmlspecialchars($clearCSS) . '" href="' . htmlspecialchars($image) . '" class="highslide imageFlottante" onclick="return hs.expand(this)">
		<img src="' . htmlspecialchars($opti ? $opti : $image) . '" title="Click to enlarge"/></a>';
}

function imgPanneau($lignes, $largeur, $clear = false)
{
    $hauteur = $largeur / 2;
    $clearCSS = $clear ? "clear: right;" : "";
    
    echo '<span class="panneau imageFlottante" style="width: ' . htmlspecialchars($largeur) . 'px; height: ' . htmlspecialchars($hauteur) . 'px; background-size: ' . htmlspecialchars($largeur . 'px ' . $hauteur . 'px') . ';' . htmlspecialchars($clearCSS) . 'font-size: ' . htmlspecialchars(round($largeur * (50 / 512))) . 'px; line-height: ' . htmlspecialchars(round($largeur * (56 / 512))) . 'px; padding-top: ' . htmlspecialchars(round($largeur * (31 / 512))) . 'px;">';
    
    foreach ($lignes as $l) {
        echo htmlspecialchars($l) . '<br />';
    }
    echo '</span>';
}

function filtrerNomFichier($nom)
{
    return preg_replace("#[\/\?\*:;{}\\\"'|&%+\#~.@()[\]!,^<>\\\]#", "_", $nom);
}

function validationFantomesActivee()
{
    global $bdd;
    $reponse = $bdd->query("SELECT valeur FROM config WHERE cle = 'validation-fantomes'") or die("SQL error 553");
    $va = $reponse->fetch()["valeur"] === "true";
    $reponse->closeCursor();
    return $va;
}

function afficherBarrePartage($texte = false)
{
    $page = $_SERVER['REQUEST_URI'];
    ?>
<div class="partage">
	<!-- Facebook -->
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.7&appId=184572781901615";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<div class="fb-share-button"
		data-href="https://creativeparkour.net/<?php echo htmlspecialchars($page); ?>"
		data-layout="button" data-size="large" data-mobile-iframe="true">
		<a class="fb-xfbml-parse-ignore" target="_blank"
			href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fcreativeparkour.net%2F<?php echo urlencode(htmlspecialchars($page)); ?>&amp;src=sdkpreparse">Share</a>
	</div>

	<!-- Twitter -->
	<a href="https://twitter.com/share" class="twitter-share-button"
		data-size="large">Tweet</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
	<?php if ($texte) { ?><p>Found this useful? Share this page with other
		players!</p><?php } ?>
</div>
<?php
}

function getFichierTete($uuid, $taille = 0)
{
    if ($taille)
        return file_exists("/home/creativeeb/www/images/joueurs/" . htmlspecialchars($taille) . "/" . htmlspecialchars($uuid) . ".png") ? "images/joueurs/" . htmlspecialchars($taille) . "/" . htmlspecialchars($uuid) . ".png" : "images/joueurs/" . htmlspecialchars($taille) . "/default.png";
    else
        return file_exists("/home/creativeeb/www/images/joueurs/" . htmlspecialchars($taille) . "/" . htmlspecialchars($uuid) . ".png") ? "images/joueurs/" . htmlspecialchars($uuid) . ".png" : "images/joueurs/default.png";
}

function nomTete($uuid)
{
    $nom = getNomMC($uuid);
    return '<span class="nowrap">' . htmlspecialchars($nom) . '&nbsp;<img class="miniTete" src="' . htmlspecialchars(getFichierTete($uuid, 17)) . '" alt="' . htmlspecialchars($nom) . '\'s head"></span>';
}

/**
 * Retourne la version minimale de Minecraft qu'il faut pour jouer sur la map (classe Map) donnée (sans le "1.")
 *
 * @param unknown $map
 * @return Array Clé "ver" : version; clé "détails" : liste des trucs incompatibles
 */
function versionMinMap($map)
{
    $blocs1_9 = [
        "END_ROD",
        "END_BRICKS",
        "PURPUR_BLOCK",
        "PURPUR_PILLAR",
        "PURPUR_STAIRS",
        "PURPUR_DOUBLE_SLAB",
        "PURPUR_SLAB",
        "CHORUS_FLOWER",
        "CHORUS_PLANT",
        "FROSTED_ICE",
        "BEETROOT_BLOCK",
        "GRASS_PATH",
        "STRUCTURE_BLOCK"
    ];
    $blocs1_10 = [
        "MAGMA",
        "NETHER_WART_BLOCK",
        "RED_NETHER_BRICK",
        "BONE_BLOCK",
        "STRUCTURE_VOID"
    ];
    $blocs1_11 = [
        "WHITE_SHULKER_BOX",
        "ORANGE_SHULKER_BOX",
        "MAGENTA_SHULKER_BOX",
        "LIGHT_BLUE_SHULKER_BOX",
        "YELLOW_SHULKER_BOX",
        "LIME_SHULKER_BOX",
        "PINK_SHULKER_BOX",
        "GRAY_SHULKER_BOX",
        "SILVER_SHULKER_BOX",
        "CYAN_SHULKER_BOX",
        "PURPLE_SHULKER_BOX",
        "BLUE_SHULKER_BOX",
        "BROWN_SHULKER_BOX",
        "GREEN_SHULKER_BOX",
        "RED_SHULKER_BOX",
        "BLACK_SHULKER_BOX",
        "OBSERVER"
    ];
    $blocs1_12 = [
        "CONCRETE",
        "CONCRETE_POWDER",
        "BED_BLOCK", // Les lits rebondissent en 1.12
        "WHITE_GLAZED_TERRACOTTA ",
        "ORANGE_GLAZED_TERRACOTTA ",
        "MAGENTA_GLAZED_TERRACOTTA ",
        "LIGHT_BLUE_GLAZED_TERRACOTTA ",
        "YELLOW_GLAZED_TERRACOTTA ",
        "LIME_GLAZED_TERRACOTTA ",
        "PINK_GLAZED_TERRACOTTA ",
        "GRAY_GLAZED_TERRACOTTA ",
        "SILVER_GLAZED_TERRACOTTA ",
        "CYAN_GLAZED_TERRACOTTA ",
        "PURPLE_GLAZED_TERRACOTTA ",
        "BLUE_GLAZED_TERRACOTTA ",
        "BROWN_GLAZED_TERRACOTTA ",
        "GREEN_GLAZED_TERRACOTTA ",
        "RED_GLAZED_TERRACOTTA ",
        "BLACK_GLAZED_TERRACOTTA "
    ];
    $blocsHitbox1_9 = [
        "IRON_FENCE",
        "THIN_GLASS",
        "STAINED_GLASS_PANE"
    ];
    $speciaux1_9 = [
        "LEVITATION",
        "ELYTRA"
    ];
    $nonConvertibles = [
        "END_ROD",
        "CHORUS_FLOWER",
        "CHORUS_PLANT",
        "FROSTED_ICE",
        "OBSERVER",
        "IRON_FENCE",
        "THIN_GLASS",
        "STAINED_GLASS_PANE",
        "LEVITATION",
        "ELYTRA",
        "GRASS_PATH",
        "BED_BLOCK"
    ];
    
    $ver = 8; // On commence à 1.8
              // Vérification des blocs
    foreach ($map->blocs as $coords => $bloc) {
        if (! in_array($bloc, $problemes)) {
            if (in_array($bloc, $blocs1_9)) {
                if ($ver < 9)
                    $ver = 9;
                $problemes[] = $bloc;
            } else if (in_array($bloc, $blocs1_10)) {
                if ($ver < 10)
                    $ver = 10;
                $problemes[] = $bloc;
            } else if (in_array($bloc, $blocs1_11)) {
                if ($ver < 11)
                    $ver = 11;
                $problemes[] = $bloc;
            } else if (in_array($bloc, $blocs1_12)) {
                if ($ver < 12)
                    $ver = 12;
                $problemes[] = $bloc;
            } else if (in_array($bloc, $blocsHitbox1_9)) {
                // Si tous les blocs autour sont de l'air, map incompatible avec la 1.8
                $c = explode(";", $coords);
                if ($map->getBloc($c[0] - 1, $c[1], $c[2]) === "AIR" && $map->getBloc($c[0] + 1, $c[1], $c[2]) === "AIR" && $map->getBloc($c[0], $c[1], $c[2] - 1) === "AIR" && $map->getBloc($c[0], $c[1], $c[2] + 1) === "AIR") {
                    if ($ver < 9)
                        $ver = 9;
                    $problemes[] = $bloc;
                }
            }
        }
    }
    
    // Vérification des blocs spéciaux
    foreach ($map->speciaux as $sp) {
        if ($sp->t === "effects" && in_array($sp->effect, $speciaux1_9) && ! in_array($sp->effect, $problemes)) {
            if ($ver < 9)
                $ver = 9;
            $problemes[] = $sp->effect;
            break;
        } else if ($sp->t === "gives" && in_array($sp->type, $speciaux1_9) && ! in_array($sp->type, $problemes)) {
            if ($ver < 9)
                $ver = 9;
            $problemes[] = $sp->type;
            break;
        }
    }
    
    // Recherche de la version vers laquelle la map peut être convertie
    $verConv = 8;
    if ($ver > 8) {
        foreach ($problemes as $p) {
            // Si le problème n'est pas convertible en 1.8, on recherche la version où il est apparu
            if (in_array($p, $nonConvertibles)) {
                if ($verConv < 9 && (in_array($p, $blocs1_9) || in_array($p, $blocsHitbox1_9) || in_array($p, $speciaux1_9)))
                    $verConv = 9;
                else if ($verConv < 10 && in_array($p, $blocs1_10))
                    $verConv = 10;
                else if ($verConv < 11 && in_array($p, $blocs1_11))
                    $verConv = 11;
                else if ($verConv < 12 && in_array($p, $blocs1_12))
                    $verConv = 12;
            }
        }
    }
    
    $r["ver"] = $ver;
    $r["détails"] = implode(";", $problemes);
    $r["verConv"] = $verConv;
    return $r;
}

/**
 * Transforme une chaîne de caractères (trucs séparés par ":" et ";") en un tableau structuré
 *
 * @param unknown $str
 *            Chaîne venant de la base de données ou des stats du serveur.
 * @return Tableau (clé=joueur, valeur=nbTentatives)
 */
function stringToArray($str)
{
    if (! $str)
        return array();
    foreach (explode(";", $str) as $j) {
        $explode = explode(":", $j);
        $a[$explode[0]] = $explode[1];
    }
    return $a;
}

/**
 * Transforme un tableau de trucs en une chaîne de caractères stockable (trucs séparés par ":" et ";").
 *
 * @param unknown $arr
 * @return string
 */
function arrayToString($arr)
{
    foreach ($arr as $uuid => $nb) {
        $str .= $uuid . ":" . $nb . ";";
    }
    if ($str)
        return substr($str, 0, strlen($str) - 1);
    return "";
}

function tentativesToArray($tentatives)
{
    return stringToArray($tentatives);
}

function arrayToTentatives($arr)
{
    return arrayToString($arr);
}

/**
 * Génère un sélecteur aléatoire pour fantôme
 *
 * @param unknown $id
 *            ID du fantôme
 * @return string
 */
function selecteurFantome($id)
{
    return substr(genererJeton(2) . dechex($id), 0, 8);
}

function quitterErreur($code)
{
    http_response_code($code);
    die();
}

function afficherGraph($type, $array, $nom, $options = "{}", $tri = true, $unite = "UA")
{
    global $googleCharts;
    if ($tri)
        arsort($array);
    $tabJS = "[";
    $virgule = false;
    foreach ($array as $k => $v) {
        if ($virgule)
            $tabJS .= ",";
        if (! is_numeric($v)) // Ajout de quotes autour des String
            $v = "'" . $v . "'";
        $tabJS .= "['" . addslashes(htmlspecialchars($k)) . "'," . htmlspecialchars($v) . "]";
        $virgule = true;
    }
    $tabJS .= "]";
    
    // Chargement des librairies si c'est la première fois
    if (! $googleCharts) {
        echo '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load(\'current\', {\'packages\':[\'corechart\',\'geochart\']});
	</script>';
        $googleCharts = true;
    }
    
    // Affichage du graphique
    $nomSansEspace = htmlspecialchars(str_replace(" ", "", $nom));
    $nomSansEspace = preg_replace("/[^A-Za-z0-9 ]/", '_', $nomSansEspace);
    echo '<script type="text/javascript">';
    echo 'google.charts.setOnLoadCallback(draw' . $nomSansEspace . 'Chart);';
    echo "function draw" . $nomSansEspace . "Chart() {

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', '" . htmlspecialchars($unite) . "');
        data.addRows(" . htmlspecialchars($tabJS) . ");

        var options = " . htmlspecialchars($options) . ";

        var chart = new google.visualization." . htmlspecialchars($type) . "(document.getElementById('" . $nomSansEspace . "_chart_div'));
        chart.draw(data, options);
      }";
    echo "</script>\n";
    echo '<div id="' . $nomSansEspace . '_chart_div" style="display: inline-block;"></div>';
    echo "\n";
}

/**
 * Affiche un graphique de camembert avec le taleau
 *
 * @param Array $array
 *            Tableau dont les clés sont les noms des éléments et les valeurs leur effectif
 * @param String $nom
 *            Nom du graphique
 */
function afficherCamembert($array, $nom)
{
    $options = "{title:'" . htmlspecialchars(addslashes($nom)) . "',
                       width:315,
                       height:200}";
    afficherGraph("PieChart", $array, $nom, $options);
}

function afficherBarres($array, $nom, $unite = "UA", $width = 315, $height = 200)
{
    $options = "{title:'" . htmlspecialchars(addslashes($nom)) . "',
                       width:" . htmlspecialchars($width) . ",
                       height:" . htmlspecialchars($height) . "
		            	,legend: 'none',
						'chartArea': {'width': '80%', 'height': '90%'}}";
    afficherGraph("BarChart", $array, $nom, $options, true, $unite);
}

function afficherCourbe($array, $nom, $unite = "UA")
{
    afficherGraph("AreaChart", $array, $nom, "{title:'" . htmlspecialchars(addslashes($nom)) . "', width:960}", false, $unite);
}

function getPaysIP($ip)
{
    include_once ('GeoIP/geoip.inc');
    /*
     * test if $ip is v4 or v6 and assign appropriate .dat file in $gi
     * run appropriate function geoip_country_code_by_addr() vs geoip_country_code_by_addr_v6()
     */
    if ((strpos($ip, ":") === false)) {
        // ipv4
        $gi = geoip_open("GeoIP/GeoIP.dat", GEOIP_STANDARD);
        $country = geoip_country_code_by_addr($gi, $ip);
    } else {
        // ipv6
        $gi = geoip_open("GeoIP/GeoIPv6.dat", GEOIP_STANDARD);
        $country = geoip_country_code_by_addr_v6($gi, $ip);
    }
    return $country;
}

function infobulle($texte, $tooltip)
{
    return '<span class="tooltip texteInfo">' . $texte . '<span class="tooltiptext">' . htmlspecialchars($tooltip) . '</span></span>';
}

function fauxLienAction($onclick, $texte, $tooltip = null)
{
    if ($tooltip) {
        return '<span class="tooltip fauxLien"><span onclick="' . htmlspecialchars($onclick) . '">' . $texte . '</span><span class="tooltiptext">' . htmlspecialchars($tooltip) . '</span></span>';
    } else {
        return '<span class="fauxLien"><span onclick="' . htmlspecialchars($onclick) . '">' . $texte . '</span></span>';
    }
}

function getInfosMail($idUtilisateur)
{
    global $bdd;
    $reponse = $bdd->prepare("SELECT adresse, verif FROM mails WHERE idUtilisateur = :id AND principale = 1 ORDER BY id DESC LIMIT 1");
    $reponse->execute(array(
        'id' => $idUtilisateur
    )) or die("SQL error 1128");
    $infosMail = $reponse->fetch();
    $reponse->closeCursor();
    return $infosMail;
}

function avertissementMail()
{
    echo '<div class="avertissement">
		Your email has not been verified, please check your emails (also your spam folder).<br /> If you do not find anything, <a href="/user/index.php?send-email=1">click here</a> to send the verification email again.
	</div>';
}

function mailVerifie($idUtilisateur)
{
    return strlen(getInfosMail($idUtilisateur)["verif"]) == 0;
}
?>