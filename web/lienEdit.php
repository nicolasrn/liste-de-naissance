<?php
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