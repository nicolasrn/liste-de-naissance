<?php
	require_once("ArticlesRestHandler.php");
	require_once("LoginRestHandler.php");

	$restHandler = null;
	$model = $_REQUEST["model"];

	switch($model) {
		case "articles":
			$restHandler = new ArticlesRestHandler();
			break;
		case "login":
			$restHandler = new LoginRestHandler();
			break;
	}

	$res = null;
	if ($restHandler != null) {
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$res = $restHandler->handleGet($_GET);
		} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$res = $restHandler->handlePost($_POST);
		}

		if ($res != null) {
			echo $res;
		}
	} else {
		header("HTTP/1.1. 500 Internal Server Error");
	}
?>