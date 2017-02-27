<?php
	require_once('services/LoginRestHandler.php');

	$loginRestHandler = new LoginRestHandler('');
	if ($loginRestHandler->getUtilisateurCourantAsAdmin($_COOKIE)) {
		header("HTTP/1.1 200 OK");
		header("Content-Type: text/html");
		
		$buffer = "";
		$lien = array();
		array_push($lien, array(
			'url' => '#/liste-de-naissance/edit',
			'label' => 'ajouter des articles'
		), array(
			'url' => '#/liste-de-naissance/detail',
			'label' => 'détail des réservations'
		), array(
			'url' => '#/personnes',
			'label' => 'personnes inscrites'
		));

		$buffer = '<ul class="nav navbar-nav">';
		foreach($lien as $key => $val) {
			$buffer = $buffer . '<li><a href="' . $val['url'] . '">' . $val['label'] . '</a></li>';
		}
		$buffer = $buffer . "</ul>";
		echo $buffer;
		exit;
	}
	header("HTTP/1.1 401 Unauthorized");
	header("Content-Type: text/html");
?>