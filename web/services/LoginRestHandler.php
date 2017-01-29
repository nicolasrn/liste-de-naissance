<?php
require_once("SimpleRest.php");
		
class LoginRestHandler extends SimpleRest {

	public function handleGet($get) {
		//var_dump($get);

		if (!isset($get['action'])) {
			$login = $get["login"];
			$password = $get["password"];

			$reponse = $this->check($login, $password);
			$_SESSION['isAuthentificate'] = true;
		} else {
			if ($get['action'] == "isAuthentificate") {
				$reponse = array('isAuthentificate' => $_SESSION['isAuthentificate']);
			}
		}

		return json_encode($reponse, JSON_FORCE_OBJECT);
	}

	private function check($login, $password) {
		try {
			$bdd = new PDO('mysql:host=localhost;dbname=liste-de-naissance;charset=utf8', 'client', 'client');
		} catch(Exception $e) {
			die('Erreur : '.$e->getMessage());
		}

		$req = $bdd->prepare('select * from tpersonne where login = :login and password = :password');
		$req->execute(array(
			'login' => $login,
			'password' => $password
		));

		if ($req->rowCount() == 1) {
			return array(
				'login' => $login,
				'password' => $password
			);	
		}
		return null;
	}

}
?>