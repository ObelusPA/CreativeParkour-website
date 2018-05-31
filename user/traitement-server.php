<?php
if ($_SESSION ["cle"]) // Si le mec a une clé, on l'ajoute aux propriétaires du serveur s'il n'y est pas déjà
{
	// Recherche d'un serveur correspondant à cette clé
	$reponse = $bdd->prepare ( "SELECT id, uuid, idsProprietaires FROM serveurs WHERE cleID = :cleID ORDER BY id DESC LIMIT 1" );
	$reponse->execute ( array (
			'cleID' => $_SESSION ["cle"]->id 
	) ) or die ( "SQL error 8" );
	$donnees = $reponse->fetch ();
	$reponse->closeCursor ();
	if (! $donnees) {
		$_SESSION ["erreurs"] [] = "Something wrong happened with your request, please try again.";
		header ( "Location: ../" );
		die ();
	} else {
		$servID = $donnees ["id"];
		$proprietaires = explode ( ";", $donnees ["idsProprietaires"] );
		$prefixe = $donnees ["idsProprietaires"] . ";";
		if (! $donnees ["idsProprietaires"])
			$prefixe = "";
		if (! in_array ( $_SESSION ["utilisateur"]->id, $proprietaires )) { // Si le mec n'est pas déjà dans les propriétaires, on l'ajoute
			$req = $bdd->prepare ( "UPDATE serveurs SET idsProprietaires = :proprietaires, cleID = 0 WHERE id = :servID" );
			$req->execute ( array (
					'proprietaires' => $prefixe . $_SESSION ["utilisateur"]->id,
					'servID' => $servID 
			) ) or die ( "SQL error 26" );
			$req->closeCursor ();
		}
	}
	// On renvoie le mec aux paramètres du serveur
	unset ( $_SESSION ["cle"] );
	header ( "Location: server.php?id=" . htmlspecialchars ( $servID ) );
	die ( "Redirecting..." );
}

// Récupération des données du serveur en fonction de l'ID en GET
if ($_GET ["id"]) {
	$reponse = $bdd->prepare ( "SELECT id, etat, ip, idsProprietaires, nom, description, siteWeb FROM serveurs WHERE id = :id ORDER BY id DESC LIMIT 1" );
	$reponse->execute ( array (
			'id' => $_GET ["id"] 
	) ) or die ( "SQL error 41" );
	$infos = $reponse->fetch ();
	$reponse->closeCursor ();
	
	// Si le mec n'est pas dans les propriétaires, on le jette
	if (! in_array ( $_SESSION ["utilisateur"]->id, explode ( ";", $infos ["idsProprietaires"] ) )) {
		http_response_code ( 403 );
		$_SESSION ["erreurs"] [] = "Unauthorized";
		header ( "Location: ../" );
		die ();
	} else if ($infos) {
		
		// Enregistrement des données du formulaire si elles sont valides
		if ($_POST ["save"]) {
			$localhost = isset ( $_POST ["localhost"] );
			if (! $localhost && ! strpos ( $_POST ["ip"], "." )) {
				$_SESSION ["erreurs"] [] = "Invalid IP";
			}
			if (strlen($_POST ["ip"]) > 255)
				$_SESSION ["erreurs"] [] = "Your server IP is too long";
			if (! $_POST ["nom"]) {
				$_SESSION ["erreurs"] [] = "You must choose a name for your server";
			} else if (!$localhost && (strtolower ( $_POST ["nom"] ) === "a minecraft server" || strtolower ( $_POST ["nom"] ) === "unknown server")) {
				$_SESSION ["erreurs"] [] = "Please choose a different server name";
			} else if (strlen($_POST ["nom"]) > 60)
				$_SESSION ["erreurs"] [] = "Your server name is too long (maximum 60 characters)";
			if (! $localhost && $_POST ["public"] === "Yes") {
				if (! $_POST ["description"])
					$_SESSION ["erreurs"] [] = "Please write a short description of your server";
				else if (strlen($_POST ["description"]) > 800)
					$_SESSION ["erreurs"] [] = "Your description is too long";
			}
			if ($_POST["siteWeb"]) {
				if (! filter_var ( $_POST ["siteWeb"], FILTER_VALIDATE_URL ))
					$_SESSION ["erreurs"] [] = "Please enter a valid website URL";
				if (strlen($_POST["siteWeb"]) > 255)
					$_SESSION ["erreurs"] [] = "Your website URL is too long";
			}
			
			if (! $_SESSION ["erreurs"]) {
				// Enregistrement des données
				$req = $bdd->prepare ( "UPDATE serveurs SET etat = :etat, ip = :ip, nom = :nom, description = :description, siteWeb = :siteWeb WHERE id = :servID" );
				$req->execute ( array (
						'etat' => $_POST ["public"] === "Yes" ? "public" : "prive",
						'ip' => $localhost ? "localhost" : $_POST ["ip"],
						'nom' => $_POST ["nom"],
						'description' => $localhost ? "" : $_POST ["description"],
						'siteWeb' => $localhost ? "" : $_POST ["siteWeb"],
						'servID' => $infos ["id"] 
				) ) or die ( "SQL error 93" );
				$req->closeCursor ();
				
				$_SESSION ["msgOK"] [] = "Saved data for \"" . htmlspecialchars ( $_POST ["nom"] ) . "\".";
				if ($infos ["etat"] === "creation") {
					$_SESSION ["msgOK"] [] = "You can now download and share parkour maps on your server! Browse the map list and download maps to play (you can do it in game, type \"/cp play\" to see downloadable maps). Type \"/cp share\" in maps you created to contribute. If you set your server as public, it will be displayed on the website as soon as you share a map.";
				}
				header ( "Location: server.php?id=" . htmlspecialchars ( $infos ["id"] ) );
				die ( "Redirecting..." );
			}
		}
	}
}
?>