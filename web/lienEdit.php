<?php
	//var_dump($_COOKIE);
	if (isset($_COOKIE['ldn-user'])) {
		$user = json_decode($_COOKIE['ldn-user']);
		$userAdmin = array('dev', 'nicopapa', 'laurencemaman');

		if (in_array($user->user->login, $userAdmin)) {
			header("HTTP/1.1 200 OK");
			header("Content-Type: text/html");
			
			echo '#/liste-de-naissance/edit/' . $_GET['id'];
			exit;
		}
	}

	header("HTTP/1.1 401 Unauthorized");
	header("Content-Type: text/html");
?>