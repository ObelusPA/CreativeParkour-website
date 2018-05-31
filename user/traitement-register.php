<?php
if ($_SESSION ["cle"]) // Si le mec vient avec une clé, on la supprime, sinon, dehors
{
	unset ( $_SESSION ["cle"] );
	
	if (! connecte ()) {
		$_SESSION ["erreurs"] [] = "Sorry, you are not properly logged in. Please try again.";
		header ( "Location: ../" );
		die ();
	}
} else {
	header ( "Location: ../" );
	die ();
}
?>