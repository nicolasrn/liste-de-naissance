<?php
	//var_dump($_COOKIE);
	function getLienEdition($id, $libelle) {
		if (isset($_COOKIE['ldn-user'])) {
			$user = json_decode($_COOKIE['ldn-user']);
			$userAdmin = array('dev', 'nicopapa', 'laurencemaman');
			
			if (in_array($user->user->login, $userAdmin)) {
				$lien = '#/liste-de-naissance/edit/' . $id;
				return "<a href='" . $lien . "'>" . $libelle . "</a>";
			} else {
				return $libelle;
			}
		}
		return null;
	}
?>