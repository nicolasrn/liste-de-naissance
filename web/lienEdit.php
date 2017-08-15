<?php
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	require_once('services/LoginRestHandler.php');

	function getLienEdition($id, $libelle) {
		$loginRestHandler = new LoginRestHandler('');
		if ($loginRestHandler->getUtilisateurCourantAsAdmin($_COOKIE)) {
			$lien = '#/liste-de-naissance/edit/' . $id;
			return "<a href='" . $lien . "'>" . $libelle . "</a>";
		}
		return $libelle;
	}
?>