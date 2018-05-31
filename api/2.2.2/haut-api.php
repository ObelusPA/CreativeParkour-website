<?php
require_once '../../includes/haut-global.php';

// Vérification du mode maintenance
$reponse = $bdd->query ( "SELECT valeur FROM config WHERE cle = 'maintenance-api'" ) or die ( "Service unavailable" );
$donnees = $reponse->fetch ();
$reponse->closeCursor ();
if ($donnees ["valeur"] === "true" && ! in_array ( getUserIP (), MAINTENANCE_BYPASS_IPS )) {
	http_response_code ( 503 );
	die ();
}

verifOrigine ();
header ( 'Content-Type: application/json' );

// Fonctions

/**
 * Arrête le script et bannit l'utilisateur s'il n'est pas le plugin Java
 */
function verifOrigine() {
	if (strpos ( $_SERVER ["HTTP_USER_AGENT"], "CreativeParkour" ) === FALSE) {
		bannir ( time () + 24 * 3600, "security reasons (201)", getUserIP () );
		http_response_code ( 403 );
		die ();
	}
}

/**
 * Ajoute l'élément $data à la variable $retourAPI["data"][$cle]
 */
function ajouterReponse($cle, $data) {
	global $retourAPI;
	$retourAPI ["data"] [$cle] = $data;
}
/**
 * Renvoie une erreur et termine le script
 * 
 * @param unknown $raison
 *        	Raison de l'erreur
 */
function erreurAPI($raison) {
	global $retourAPI;
	$retourAPI ["STATUS"] = "ERROR";
	$retourAPI ["error reason"] = htmlspecialchars ( $raison );
	repondre ();
}

/**
 * Envoie à l'utilisateur le contenu de la variable $retourAPI en JSON et termine le script
 */
function repondre() {
	global $retourAPI;
	echo json_encode ( $retourAPI );
	die ();
}

// Variable de trucs à retourner
$retourAPI = [ 
		"STATUS" => "OK",
		"data" => [ ] 
];

// Décompression des données
parse_str ( gzinflate ( substr ( file_get_contents ( "php://input" ), 10, - 8 ) ), $_POST );

// Enregistrement de la connexion
$req = $bdd->prepare ( "INSERT INTO connexions SET date = NOW(), uuidServeur = :uuidServeur, ip = :ip, infos = :infos, page = :page, codeHTTP = :codeHTTP, https = :https" );
$req->execute ( array (
		'uuidServeur' => $_POST ["uuidServ"] ? $_POST ["uuidServ"] : "",
		'ip' => getUserIP (),
		'infos' => $_SERVER ['HTTP_USER_AGENT'],
		'page' => $_SERVER ['REQUEST_URI'],
		'codeHTTP' => http_response_code (),
		'https' => $_SERVER ['HTTPS'] ? 1 : 0 
) ) or die ( "SQL error 75" );
$req->closeCursor ();

// Bannissement du mec s'il n'envoie pas uuidServ
// if (! $_POST ["uuidServ"]) {
// bannir ( "infini", "202", getUserIP () );
// http_response_code ( 403 );
// die ();
// }

if (verifBan ( true, $_POST ["ipJoueur"], $_POST ["uuidJoueur"] )) {
	erreurAPI ( "Forbidden" );
}

$serveurTest = $_POST ["uuidServ"] == "bd514601-c2f6-4401-8340-6289d30db46d";
?>