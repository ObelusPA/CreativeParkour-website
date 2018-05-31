<?php
require_once '../includes/haut-global.php';

// Vérification de si le mec est propriétaire du serveur
if (! $_GET ["serv"])
	quitterErreur ( 400 );
else {
	debut:
	
	$reponse = $bdd->prepare ( "SELECT idsProprietaires, nom, tricheursIgnores, operateurs FROM serveurs WHERE id = :id ORDER BY id DESC LIMIT 1" );
	$reponse->execute ( array (
			'id' => $_GET ["serv"] 
	) ) or die ( "SQL error 13" );
	$infosServ = $reponse->fetch ();
	$reponse->closeCursor ();
	
	// Si le mec n'est pas dans les propriétaires, on le jette
	if (! in_array ( $_SESSION ["utilisateur"]->id, explode ( ";", $infosServ ["idsProprietaires"] ) )) {
		quitterErreur ( 403 );
	}
	
	$tricheursIgnores = array_filter ( explode ( ";", $infosServ ["tricheursIgnores"] ) );
	$operateurs = array_filter ( explode ( ";", $infosServ ["operateurs"] ) );
	
	// ///////////////////////////////////////////////
	// Traitement des actions
	
	if ($_GET ["donnees"] == "ignores") {
		// Liste des ignorés
		
		if ($_GET ["uuidJoueur"]) {
			unset ( $tricheursIgnores [array_search ( $_GET ["uuidJoueur"], $tricheursIgnores )] );
			$req = $bdd->prepare ( "UPDATE serveurs SET tricheursIgnores = :ti WHERE id = :idServ" );
			$req->execute ( array (
					'ti' => implode ( ";", $tricheursIgnores ),
					'idServ' => $_GET ["serv"] 
			) ) or die ( "SQL error 37" );
			$req->closeCursor ();
		}
		
		if (! $tricheursIgnores)
			echo '<em>Nobody to display here...</em>';
		foreach ( $tricheursIgnores as $j ) {
			echo '&nbsp;&bull; ' . fauxLienAction ( "designorerJoueur('" . htmlspecialchars ( $j ) . "')", nomTete ( $j ) ) . '<br />';
		}
	} else if ($_GET ["donnees"] == "params") {
		// Paramètres de notification
		
		// Si modification des paramètres
		if ($_GET ["notifsJeu"] && $_GET ["notifsMail"]) {
			$notifsJeu = $_GET ["notifsJeu"] == "true";
			$notifsMail = $_GET ["notifsMail"] == "true";
			$req = $bdd->prepare ( "UPDATE utilisateurs SET notifsTricheursJeu = :j, notifsTricheursMail = :m WHERE id = :id" );
			$req->execute ( array (
					'id' => $_SESSION ["utilisateur"]->id,
					'j' => $notifsJeu,
					'm' => $notifsMail 
			) ) or die ( "SQL error 58" );
			$req->closeCursor ();
			$majParams = true;
		} else {
			$reponse = $bdd->prepare ( "SELECT notifsTricheursJeu, notifsTricheursMail FROM utilisateurs WHERE id = :id" );
			$reponse->execute ( array (
					'id' => $_SESSION ["utilisateur"]->id 
			) ) or die ( "SQL error 65" );
			$d = $reponse->fetch ();
			$reponse->closeCursor ();
			
			$notifsJeu = $d ["notifsTricheursJeu"];
			$notifsMail = $d ["notifsTricheursMail"];
		}
		
		echo '<input id="notifsJeu" type="checkbox" onchange="reglagesNotifs()" ';
		if ($notifsJeu)
			echo 'checked';
		echo '><label
			for="notifsJeu">Receive notifications in Minecraft about cheaters on your server</label><br />';
		
		echo '<input id="notifsMail" type="checkbox" onchange="reglagesNotifs()" ';
		if ($notifsMail)
			echo 'checked';
		echo '><label
			for="notifsMail">Receive emails about cheaters on your server';
		if ($notifsMail && ! mailVerifie ( $_SESSION ["utilisateur"]->id ))
			echo ' <strong>(will work as soon as you verify your email)</strong>';
		echo '</label>';
		
		if ($majParams) {
			echo '<br /><span class="texteOK" id="settingsUpdated">Settings updated!</span>';
		}
	} else {
		// Liste des tricheurs
		
		// Si on cache un fantôme
		if ($_GET ["hide"]) {
			$req = $bdd->prepare ( "UPDATE fantomes SET cache = 1 WHERE selecteur = :sel AND idServ = :idServ" );
			$req->execute ( array (
					'sel' => $_GET ["hide"],
					'idServ' => $_GET ["serv"] 
			) ) or die ( "SQL error 64" );
			$req->closeCursor ();
		}
		
		// Si on ignore un joueur
		if ($_GET ["ignorePlayer"]) {
			// Recherche du joueur correspondant au fantôme
			$reponse = $bdd->prepare ( "SELECT uuidJoueur FROM fantomes WHERE selecteur = :sel AND idServ = :idServ ORDER BY id LIMIT 1" );
			$reponse->execute ( array (
					'sel' => $_GET ["ignorePlayer"],
					'idServ' => $_GET ["serv"] 
			) ) or die ( "SQL error 42" );
			$uuid = $reponse->fetch () ["uuidJoueur"];
			$reponse->closeCursor ();
			
			if (! in_array ( $uuid, $tricheursIgnores )) {
				$req = $bdd->prepare ( "UPDATE serveurs SET tricheursIgnores = :ti WHERE id = :idServ" );
				$req->execute ( array (
						'ti' => $infosServ ["tricheursIgnores"] ? $infosServ ["tricheursIgnores"] . ";" . $uuid : $uuid,
						'idServ' => $_GET ["serv"] 
				) ) or die ( "SQL error 75" );
				$req->closeCursor ();
				
				goto debut;
			}
		}
		
		// ///////////////////////////////////////////////
		// Envoi de la liste
		
		// Récupération des infos des fantômes
		$condCache = "AND f.cache = 0";
		$reponse = $bdd->prepare ( "
			SELECT f.uuidJoueur uuidJoueur, f.ticks AS ticksR, f.ticks * 0.05 AS ticks, f.id idFantome, DATE_FORMAT(f.date, '%m-%d-%Y') date, f.rapportTriche rapportTriche, f.selecteur selecteur, f.cache cache, m.id idMap, m.nom nomMap, m.uuid uuidMap
			FROM fantomes f
			INNER JOIN maps m ON f.uuidMap = m.uuid
			WHERE f.etat = 'invalide' AND f.idServ = :serv AND f.triche = 1
			ORDER BY f.cache, f.uuidMap, f.id DESC
			" );
		$reponse->execute ( array (
				"serv" => $_GET ["serv"] 
		) ) or die ( "SQL error 141" );
		$nb = 0;
		while ( $f = $reponse->fetch () ) {
			if ((! $f ["cache"] || $_GET ["afficherCaches"]) && ! in_array ( $f ["uuidJoueur"], $tricheursIgnores ) && ! in_array ( $f ["uuidJoueur"], $operateurs )) {
				$nb ++;
				echo $f ["cache"] ? '<tr class="fondGris">' : '<tr>';
				echo '<td>' . nomTete ( $f ["uuidJoueur"] ) . '</td>';
				echo '<td><a target="_blank" href="map.php?id=' . htmlspecialchars ( $f ["idMap"] ) . '">' . htmlspecialchars ( $f ["nomMap"] ) . '</a></td>';
				echo '<td>' . htmlspecialchars ( $f ["ticks"] ) . '&nbsp;s</td>';
				echo '<td>' . htmlspecialchars ( $f ["selecteur"] ) . '</td>';
				echo '<td>' . htmlspecialchars ( $f ["date"] ) . '</td>';
				echo '<td>';
				if ($f ["rapportTriche"] && $f ["rapportTriche"] != "ok") {
					$rapport = stringToArray ( $f ["rapportTriche"] );
					echo 'Cheat detected at: ';
					$virgule = false;
					foreach ( $rapport as $tick => $truc ) {
						if ($virgule)
							echo ', ';
						$virgule = true;
						echo htmlspecialchars ( $tick * 0.05 ) . '&nbsp;s';
					}
				}
				echo '</td><td>';
				if (! $f ["cache"])
					echo fauxLienAction ( "cacherFantome('" . htmlspecialchars ( $f ["selecteur"] ) . "')", "❌Hide", "Mark this ghost as \"checked\" and stop sending notifications about it." );
				echo " ";
				echo fauxLienAction ( "ignorerJoueur('" . htmlspecialchars ( $f ["selecteur"] ) . "')", "🛑Ignore player", "No longer consider this player as a cheater and stop sending notifications about them." );
				// echo '</td><td><input id="selectionF' . htmlspecialchars ( htmlspecialchars ( $f ["selecteur"] ) ) . '" type="checkbox">';
				echo '</td></tr>';
			}
		}
		// Si rien, message
		if ($nb == 0) {
			echo '<tr><td colspan="100%"><em>No cheater ghost to review on your server.</em></td></tr>';
		}
		$reponse->closeCursor ();
	}
}
?>