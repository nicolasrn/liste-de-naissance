<?php
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	require_once("ArticlesRestHandler.php");
	require_once("LoginRestHandler.php");

	$restHandler = null;

	function getModelAction() {
		$model = null;
		$action = null;
		if (isset($_REQUEST["model"])) {
			$model = $_REQUEST["model"];
			if (isset($_REQUEST["action"])) {
				$action = $_REQUEST["action"];
			}
		} else if (isset($_POST["model"])) {
			$model = $_POST["model"];
			if (isset($_POST["action"])) {
				$action = $_POST["action"];
			}
		} else if (isset($_GET["model"])) {
			$model = $_GET["model"];
			if (isset($_GET["action"])) {
				$action = $_GET["action"];
			}
		} else {
			header("HTTP/1.1. 400 Bad Request");
			exit;
		}
		return array('model' => $model, 'action' => $action);
	}

	$dispatcher = getModelAction();
	
	switch($dispatcher['model']) {
		case "articles":
			$restHandler = new ArticlesRestHandler($dispatcher['action']);
			break;
		case "login":
			$restHandler = new LoginRestHandler($dispatcher['action']);
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