<?php
// ini_set ( 'display_errors', 1 );
// ini_set ( 'display_startup_errors', 1 );
// error_reporting ( E_WARNING );
// error_reporting ( E_ERROR );
// error_reporting ( E_COMPILE_WARNING );
// error_reporting ( E_COMPILE_ERROR );
define ( "MAINTENANCE_BYPASS_IPS", [ ] );

session_start ();

// Connexion à la base de données
try {
	$bdd = new PDO ( 'mysql:host=############;dbname=############', '############', '############', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
} catch ( Exception $e ) {
	http_response_code ( 500 );
	require_once 'fonctions.php';
	pageErreur ( "This website is not currently available, probably because of maintenance on the servers. Please come back in a few minutes. 🙂" );
	envoyerMail ( "############", "Erreur de base de données", "Une erreur est survenue lors d'une connexion à la base de données.<br />
		 IP de l'utilisateur : " . htmlspecialchars ( getUserIP () ) . "<br />
		 Date : " . date ( 'd/m/Y à H:i:s', time () ) . "<br />
		 Page : " . htmlspecialchars ( $_SERVER ["REQUEST_URI"] ) . "<br />
		 Exception : " . htmlspecialchars ( $e->getMessage () ) );
	die ();
}

// Vérification du mode maintenance
$reponse = $bdd->query ( "SELECT valeur FROM config WHERE cle = 'maintenance-globale'" ) or die ( "Service unavailable" );
$donnees = $reponse->fetch ();
$reponse->closeCursor ();
$array = explode ( ":", $donnees ["valeur"] );
if ($array [0] === "true") {
	require_once 'fonctions.php';
	if (! in_array ( getUserIP (), MAINTENANCE_BYPASS_IPS )) {
		http_response_code ( 503 );
		pageErreur ( "Sorry, the CreativeParkour website is currently in maintenance mode. Please come back later.<br /> Maintenance reason : <em>" . htmlspecialchars ( $array [1] ) . "</em>" );
		die ();
	}
}

// ///////////////////////////////////////////////////////////////////////////////////
// Classes
// ///////////////////////////////////////////////////////////////////////////////////
class Utilisateur {
	public $id = null;
	public $mail = null;
	public $nom = null;
	public $facebookID = null;
	public $twitterID = null;
	public $googleID = null;
	public $discordID = null;
	public $minecraftUUID = null;
	public function __construct($id, $mail, $nom, $minecraftUUID, $facebookID, $twitterID, $googleID, $discordID) {
		$this->id = $id;
		$this->mail = $mail;
		$this->nom = $nom;
		$this->facebookID = $facebookID;
		$this->twitterID = $twitterID;
		$this->googleID = $googleID;
		$this->discordID = $discordID;
		$this->minecraftUUID = $minecraftUUID;
	}
}
class Cle {
	public $id = null;
	public $cle = null;
	public $ipJoueur = null;
	public $uuidJoueur = null;
	public function __construct($id, $cle, $ipJoueur, $uuidJoueur) {
		$this->id = $id;
		$this->cle = $cle;
		$this->ipJoueur = $ipJoueur;
		$this->uuidJoueur = $uuidJoueur;
	}
	public function supprimer() {
		global $bdd;
		$req = $bdd->prepare ( "DELETE FROM cles WHERE id = :id" );
		$req->execute ( array (
				'id' => $this->id 
		) ) or die ( "SQL error 51" );
		$req->closeCursor ();
	}
}
/**
 * Réprésente une map avec son contenu (blocs normaux et spéciaux).
 */
class Map {
	public $uuid = null;
	public $blocs = null; // Les clés sont des String des coordonnées séparées par des points-virgule, les valeurs sont les types des blocs
	public $speciaux = null; // Pareil, sauf que les valeurs sont l'élément Json (sans les coordonnées du coup)
	public $types = null; // Liste des types des blocs
	public function __construct($uuid, $contenu = null) {
		global $bdd;
		$this->uuid = $uuid;
		
		if (! $contenu) {
			// Ouverture du fichier de contenu de la map
			$reponse = $bdd->prepare ( "SELECT fichierContenu FROM maps WHERE uuid = :uuidMap" );
			$reponse->execute ( array (
					"uuidMap" => $uuid 
			) ) or die ( "SQL error 126" );
			$donnees = $reponse->fetch ();
			$reponse->closeCursor ();
			if ($donnees) {
				$contenu = "";
				$zp = gzopen ( "/home/creativeeb/www/maps data/" . $donnees ["fichierContenu"] . ".gz", 'r' );
				if (is_bool ( $zp ))
					die ( "GZ error" );
				while ( ! gzeof ( $zp ) ) {
					$contenu .= gzread ( $zp, 4096 );
				}
				gzclose ( $zp );
			}
		}
		$json = json_decode ( $contenu );
		unset ( $contenu );
		$listeBlocs = $json->blocs;
		$this->types = $json->types;
		// Remplissage de la liste des blocs avec leurs types
		foreach ( $listeBlocs as $b ) {
			// Recherche de ce bloc dans les types
			foreach ( $this->types as $type ) {
				if ($type->i == $b->i)
					$this->blocs [$b->c] = $type->t;
			}
		}
		
		// Remplissage du tableau des blocs spéciaux
		$listeSpeciaux = $json->{'blocs speciaux'};
		foreach ( $listeSpeciaux as $sp ) {
			if (is_array ( $sp )) {
				foreach ( $sp as $sp2 ) {
					$coords = $sp2->c;
					unset ( $sp2->c );
					$this->speciaux [$coords] = $sp2;
				}
			} else {
				$coords = $sp->c;
				unset ( $sp->c );
				$this->speciaux [$coords] = $sp;
			}
		}
	}
	public function getBloc($x, $y, $z) {
		$b = $this->blocs [( int ) $x . ";" . ( int ) $y . ";" . ( int ) $z];
		if ($b == null)
			return "AIR";
		return $b;
	}
	public function estCheckpoint($x, $y, $z) {
		$b = $this->speciaux [( int ) $x . ";" . ( int ) $y . ";" . ( int ) $z];
		if ($b == null || $b->t != "checkpoints")
			return false;
		return true;
	}
	public function estTP($x, $y, $z) {
		foreach ( $this->getSpeciaux ( $x, $y, $z ) as $b ) {
			if ($b->t == "tp")
				return true;
		}
		return false;
	}
	public function getSpeciaux($x, $y, $z) {
		$iX = ( int ) $x; // Partie entière
		$iY = ( int ) $y;
		$iZ = ( int ) $z;
		$a [] = $this->speciaux [$iX . ";" . $iY . ";" . $iZ];
		
		// Blocs adjacents si on est sur le bord (0.3)
		$dX = $x - $iX; // Partie décimale
		$dY = $y - $iY;
		$dZ = $z - $iZ;
		if ($dX >= 0.25)
			$a [] = $this->speciaux [($iX + 1) . ";" . $iY . ";" . $iZ];
		if ($dX <= 0.75)
			$a [] = $this->speciaux [($iX - 1) . ";" . $iY . ";" . $iZ];
		if ($dY >= 0.87)
			$a [] = $this->speciaux [$iX . ";" . ($iY + 1) . ";" . $iZ];
		if ($dZ >= 0.25)
			$a [] = $this->speciaux [$iX . ";" . $iY . ";" . ($iZ + 1)];
		if ($dZ <= 0.75)
			$a [] = $this->speciaux [$iX . ";" . $iY . ";" . ($iZ - 1)];
		
		// Remplissage d'un nouveau tableau avec que les blocs spéciaux rééls
		foreach ( $a as $b ) {
			if ($b)
				$array [] = $b;
		}
		return $array;
	}
}

/**
 * Position d'un fantôme dans une map
 */
class Position {
	public $x = null;
	public $y = null;
	public $z = null;
	public $sneak = null;
	public $elytres = null;
	public function __construct($string) {
		$a = explode ( ";", $string );
		$this->x = $a [0];
		$this->y = $a [1];
		$this->z = $a [2];
		$this->sneak = $a [5];
		$this->elytres = $a [6];
	}
	public function __toString() {
		return $this->x . ";" . $this->y . ";" . $this->z;
	}
}

// ///////////////////////////////////////////////////////////////////////////////////
// Fonctions
// ///////////////////////////////////////////////////////////////////////////////////
require_once 'fonctions.php';

verifBan ();

// L'ajout de la connexion actuelle à la base de données se fait tout en bas des pages

// Enregistrement de l'éventuelle clé dans la session et suppression de celle-ci
if ($_GET ["c"]) {
	if (! $_SESSION ["cle"] || ! password_verify ( $_GET ["c"], $_SESSION ["cle"]->cle )) {
		$reponse = $bdd->query ( "SELECT id, cle, ipJoueur, uuidJoueur FROM cles WHERE expiration >= NOW() ORDER BY id DESC" ) or die ( "SQL error 228" );
		while ( ($donnees = $reponse->fetch ()) && ! $trouve ) {
			if (password_verify ( $_GET ["c"], $donnees ["cle"] )) {
				$trouve = true;
				if ($_SESSION ["utilisateur"] && $donnees ["uuidJoueur"] !== $_SESSION ["utilisateur"]->minecraftUUID) {
					$_SESSION ["erreurs"] [] = "Something wrong happened with your request, please try again.";
					header ( "Location : logout.php" );
					die ();
				} else {
					$_SESSION ["cle"] = new Cle ( $donnees ["id"], $donnees ["cle"], $donnees ["ipJoueur"], $donnees ["uuidJoueur"] );
					$_SESSION ["cle"]->supprimer ();
				}
			}
		}
		if (! $trouve && (! $_SESSION ["cle"] || ! password_verify ( $_GET ["c"], $_SESSION ["cle"]->cle ))) {
			$erreur = true;
			unset ( $_SESSION ["cle"] );
		}
		$reponse->closeCursor ();
	}
	if ($erreur) {
		$_SESSION ["erreurs"] [] = "Your request expired, please go back to Minecraft and try again.";
		header ( "Location : ../" );
		die ();
	}
}

// Enregistrement dans la session de la redirection
if ($_GET ["return"]) {
	$_SESSION ["return"] = $_GET ["return"];
}

// Tentative de connexion automatique
if (! $_SESSION ["utilisateur"] && $_COOKIE ["login"]) {
	$cookie = $_COOKIE ["login"];
	if (! strpos ( $cookie, ":" )) {
		$erreurCritique = true;
	}
	if (! $erreurCritique) {
		$cookieA = explode ( ":", $cookie, 2 );
		$selecteur = $cookieA [0];
		$jeton = $cookieA [1];
		if (strlen ( $selecteur ) != 12 || strlen ( $jeton ) != 72) {
			$erreurCritique = true;
		}
	}
	if (! $erreurCritique) {
		// Recherche dans la table
		$reponse = $bdd->prepare ( "SELECT id, jeton, idUtilisateur FROM jetonsConnexion WHERE selecteur = :selecteur ORDER BY id DESC LIMIT 1" );
		$reponse->execute ( array (
				'selecteur' => $selecteur 
		) ) or die ( "SQL error 280" );
		$donnees = $reponse->fetch ();
		$idLigne = $donnees ["id"];
		$reponse->closeCursor ();
		if ($donnees) {
			
			if (password_verify ( $jeton, $donnees ["jeton"] )) {
				// C'est réussi, connexion
				$reponse = $bdd->prepare ( "SELECT id, nom, facebookID, twitterID, googleID, discordID, minecraftUUID FROM utilisateurs WHERE id = :id" );
				$reponse->execute ( array (
						'id' => $donnees ["idUtilisateur"] 
				) ) or die ( "SQL error 291" );
				$donnees = $reponse->fetch ();
				$reponse->closeCursor ();
				if ($donnees) {
					$_SESSION ["utilisateur"] = new Utilisateur ( $donnees ['id'], getMail ( $donnees ['id'] ), $donnees ['nom'], $donnees ['minecraftUUID'], $donnees ['facebookID'], $donnees ['twitterID'], $donnees ['googleID'], $donnees ['discordID'] );
					rememberMe (); // Réinitialisation du cookie
				} else {
					$erreurCritique = true;
				}
			} else {
				$erreurCritique = true;
			}
		}
	}
	if ($erreurCritique) {
		bannir ( time () + 3600, "Security reasons", getUserIP () ); // Bannissement pendant 1 heure
		                                                             // Vidange de la table de connexion automatique
		$bdd->exec ( "TRUNCATE TABLE jetonsConnexion" );
		// Envoi d'un mail
		
		envoyerMail ( "############", "Tentative potentielle de piratage", "Quelqu'un a tenté de se connecter à l'aide de la connexion automatique,
		 mais son cookie était invalide.<br />
		 Contenu du cookie : " . htmlspecialchars ( $cookie ) . "<br />
		 IP de l'utilisateur : " . htmlspecialchars ( getUserIP () ) . "<br />
		 Date : " . date ( 'd/m/Y à H:i:s', time () ) . "<br />
		 Variable REQUEST : " . nl2br ( print_r ( $_REQUEST, true ) ) . "<br />
		 Variable SERVER : " . nl2br ( print_r ( $_SERVER, true ) ) );
	}
}
?>