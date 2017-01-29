<?php
require_once("ArticlesRestHandler.php");
require_once("LoginRestHandler.php");

//var_dump($_GET);
//var_dump($_POST);

$restHandler = null;
if(isset($_GET["model"]) || isset($_POST["model"])) {
	$model = $_GET["model"];

	switch($model) {
		case "articles":
			$restHandler = new ArticlesRestHandler();
			break;
			
		case "login":
			$restHandler = new LoginRestHandler();
			break;
		case "isAuthentificate":
			$restHandler = new IsAuthentificate();
			break;
		case "" :
			//404 - not found;
			break;
	}
} 

//var_dump($restHandler);
if ($restHandler != null) {
	if (isset($_GET["model"])) {
		echo $restHandler->handleGet($_GET);
	} else {
		echo $restHandler->handlePost($_POST);
	}
}
?>