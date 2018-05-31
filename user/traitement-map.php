<?php
if ($_SESSION["cle"]) // Si le mec vient avec une clé, on la supprime en on redirige
{
    // Recherche d'une map correspondant à cette clé
    $reponse = $bdd->prepare("SELECT id, uuid, createur FROM maps WHERE cleID = :cleID ORDER BY id DESC LIMIT 1");
    $reponse->execute(array(
        'cleID' => $_SESSION["cle"]->id
    )) or die("SQL error 8");
    $donnees = $reponse->fetch();
    $reponse->closeCursor();
    if (! $donnees || separerUuidNom($donnees["createur"])["uuid"] !== $_SESSION["utilisateur"]->minecraftUUID) {
        $_SESSION["erreurs"][] = "The account you are logged in with does not correspond to the creator of the map you are trying to share. Please log in with the map creator's account (or sign in).";
        header("Location: ../");
        die();
    } else {
        $mapID = $donnees["id"];
        // Ajout de l'ID du créateur (on n'avait que l'UUID Minecraft pour l'instant)
        $req = $bdd->prepare("UPDATE maps SET idCreateur = :idCreateur, cleID = 0 WHERE id = :mapID");
        $req->execute(array(
            'idCreateur' => $_SESSION["utilisateur"]->id,
            'mapID' => $mapID
        )) or die("SQL error 22");
        $req->closeCursor();
    }
    // On renvoie le mec aux paramètres de la map
    unset($_SESSION["cle"]);
    header("Location: map.php?id=" . htmlspecialchars($mapID));
    die("Redirecting...");
}

// Récupération des données du serveur en fonction de l'ID en GET
if ($_GET["id"]) {
    $reponse = $bdd->prepare("
			SELECT m.id idMap, m.nom nomMap, m.description description, DATE_FORMAT(m.dateAjout, '%W, %M %d, %Y') dateAjout, m.etat etat, m.image image, s.nom nomServ
			FROM maps m
			INNER JOIN serveurs s ON m.idServOrigine = s.id
			WHERE m.id = :id AND m.idCreateur = :idUtilisateur
			ORDER BY m.id DESC LIMIT 1
			");
    $reponse->execute(array(
        'id' => $_GET["id"],
        'idUtilisateur' => $_SESSION["utilisateur"]->id
    )) or die("SQL error 43");
    $infos = $reponse->fetch();
    $reponse->closeCursor();
    
    if ($infos) {
        if ($infos["etat"] === "supprimee") {
            $_SESSION["erreurs"][] = "This map was deleted.";
            header("Location: index.php");
            die();
        } else if ($infos["etat"] === "creation") {
            // Récupération de la liste des anciennes maps du mec pour demander si c'est une nouvelle version
            $reponse = $bdd->prepare("SELECT id, nom FROM maps WHERE idCreateur = :idCreateur AND etat = 'disponible'");
            $reponse->execute(array(
                'idCreateur' => $_SESSION["utilisateur"]->id
            )) or die("SQL error 55");
            while ($donnees = $reponse->fetch()) {
                $autresMaps[$donnees["id"]] = $donnees["nom"];
            }
            $reponse->closeCursor();
        }
        
        // Enregistrement des données du formulaire si elles sont valides
        if ($_POST["save"]) {
            if ($infos["etat"] === "creation" && ! verifReCaptcha()) {
                $_SESSION["erreurs"][] = "You did not complete the CAPTCHA verification properly. Please try again.";
            } else if (strlen($_POST["description"]) > 600) {
                $_SESSION["erreurs"][] = "Your map description is too long";
            } else {
                
                // Traitement de l'image
                $fichierImage = $infos["image"];
                $image = $_FILES["image"]["name"];
                $uploadedfile = $_FILES['image']['tmp_name'];
                
                if ($image) {
                    $filename = stripslashes($_FILES['image']['name']);
                    $extension = getExtensionFichier($filename);
                    $extension = strtolower($extension);
                    if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png")) {
                        $_SESSION["erreurs"][] = "Only JPG, JPEG and PNG images are allowed";
                    } else {
                        $size = filesize($_FILES['image']['tmp_name']);
                        
                        if ($size > 3000000) {
                            $_SESSION["erreurs"][] = "Your image size must be less than 3 MB";
                        }
                        
                        if ($extension == "jpg" || $extension == "jpeg") {
                            $uploadedfile = $_FILES['image']['tmp_name'];
                            $src = imagecreatefromjpeg($uploadedfile);
                        } else if ($extension == "png") {
                            $uploadedfile = $_FILES['image']['tmp_name'];
                            $src = imagecreatefrompng($uploadedfile);
                        } else {
                            $src = imagecreatefromgif($uploadedfile);
                        }
                        
                        list ($width, $height) = getimagesize($uploadedfile);
                        
                        $newwidth = 1200;
                        $newheight = 675;
                        $tmp = imagecreatetruecolor($newwidth, $newheight);
                        
                        $newwidth1 = 250;
                        $newheight1 = 140;
                        $tmp1 = imagecreatetruecolor($newwidth1, $newheight1);
                        
                        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                        
                        imagecopyresampled($tmp1, $src, 0, 0, 0, 0, $newwidth1, $newheight1, $width, $height);
                        
                        $fichierImage = filtrerNomFichier(uniqid($infos["nomMap"] . "-")) . "." . $extension;
                        $filename = "../images/maps/" . $fichierImage;
                        $filename1 = "../images/maps/mini/" . $fichierImage;
                        
                        imagejpeg($tmp, $filename, 100);
                        imagejpeg($tmp1, $filename1, 100);
                        
                        imagedestroy($src);
                        imagedestroy($tmp);
                        imagedestroy($tmp1);
                        if (! $fichierImage)
                            $_SESSION["erreurs"][] = "Sorry, there was an error uploading your image.";
                        else {
                            // Suppression de l'ancienne image
                            if ($infos["image"]) {
                                unlink("../images/maps/" . $infos["image"]);
                                unlink("../images/maps/mini/" . $infos["image"]);
                            }
                        }
                    }
                }
            }
            
            if (! $_SESSION["erreurs"]) {
                // Enregistrement des données
                $req = $bdd->prepare("UPDATE maps SET etat = :etat, description = :description, image = :image WHERE id = :mapID");
                $req->execute(array(
                    'etat' => $_POST["supprimer"] === "Oui" ? "supprimee" : "disponible",
                    'description' => $_POST["description"],
                    'image' => $fichierImage,
                    'mapID' => $infos["idMap"]
                )) or die("SQL error 136");
                $req->closeCursor();
                
                // Suppression de l'ancienne map s'il y en a une
                if ($_POST["ancienneMap"]) {
                    $req = $bdd->prepare("UPDATE maps SET etat = 'supprimee' WHERE id = :mapID AND idCreateur = :idCreateur");
                    $req->execute(array(
                        'mapID' => $_POST["ancienneMap"],
                        'idCreateur' => $_SESSION["utilisateur"]->id
                    )) or die("SQL error 151");
                    $req->closeCursor();
                }
                
                if ($_POST["supprimer"] === "Oui") {
                    $_SESSION["msgOK"][] = "\"" . htmlspecialchars($infos["nomMap"]) . "\" has been deleted.";
                    header("Location: ../");
                    die("Redirecting...");
                } else
                    $_SESSION["msgOK"][] = "Saved data for \"" . $infos["nomMap"] . "\".";
                if ($infos["etat"] === "creation") {
                    $_SESSION["msgOK"][] = "Thank you for sharing your map with the CreativeParkour community!";
                    
                    // Avertissement par mail à l'admin
                    envoyerMail("##################", "Nouvelle map : " . htmlspecialchars($infos["nomMap"]), "Une nouvelle map a été mise en ligne par " . htmlspecialchars($_SESSION["utilisateur"]->nom) . ' : <a href="https://creativeparkour.net/map.php?id=' . htmlspecialchars($infos["idMap"]) . '">' . htmlspecialchars($infos["nomMap"]) . '</a>');
                }
                header("Location: map.php?id=" . htmlspecialchars($infos["idMap"]));
                die("Redirecting...");
            }
        }
    }
}
?>