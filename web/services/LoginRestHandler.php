<?php
	require_once("SimpleRest.php");

	class LoginRestHandler extends SimpleRest {

		public function __construct() {
			parent::__construct();

			$this->reqAuthentification = $this->bdd->prepare('select * from tpersonne where login = :login and password = :password');
			$this->reqAjoutUtilisateur = $this->bdd->prepare('insert into tpersonne (login, password) values(:login, :password)');
			$this->reqSelectPourAjoutUtilisateur = $this->bdd->prepare('select * from tpersonne where login = :login');
		}

		public function handleGet($get) {
			//var_dump($get);
			$reponse = null;
			if (isset($get['action']) && $get['action'] == "isAuthentificate") {
				if (isset($_SESSION['isAuthentificate']) && $_SESSION['isAuthentificate']) {
					$this->setHttpHeaders('application/json', 200);
					return $_SESSION['user'];
				}
			}
			$this->setHttpHeaders('application/json', 401);
			return $reponse;
		}

		public function handlePost($post) {
			$reponse = null;
			if (isset($_GET['action']) && $_GET['action'] == "enregistrement") {
				$reponse = $this->enregistrement($post['login'], $post['password']);
				$this->setHttpHeaders('application/json', $reponse['code']);
				$reponse = $reponse['message'];
			} else {
				$reponse = json_encode($this->authentification($post["login"], $post["password"]), JSON_FORCE_OBJECT);
				$_SESSION['isAuthentificate'] = true;
				$_SESSION['user'] = $reponse;
				$this->setHttpHeaders('application/json', $reponse ? 200 : 500);
			}
			return $reponse;
		}

		private function enregistrement($login, $password) {
			$res = null;
			if ($this->isUtilisateurUnique($login)) {
				$res = $this->reqAjoutUtilisateur->execute(array(
					'login' => $login,
					'password' => $password
				));	
				$res = array('message' => $res, 'code' => 200);
			} else {
				$res = array('message' => 'login déjà utilisé', 'code' => 500);
			}
			return $res;
		}

		private function isUtilisateurUnique($login) {
			$this->reqSelectPourAjoutUtilisateur->execute(array(
				'login' => $login
			));
			return $this->reqSelectPourAjoutUtilisateur->rowCount() == 0;
		}

		private function authentification($login, $password) {
			$this->reqAuthentification->execute(array(
				'login' => $login,
				'password' => $password
			));

			$response = null;
			$code = 401;
			
			if ($this->reqAuthentification->rowCount() == 1) {
				$code = 200;
				$response = array(
					'user' => array(
						'id' => $this->reqAuthentification->fetch()['id'],
						'login' => $login
					)
				);
			}
			$this->setHttpHeaders('application/json', $code);
			return $response;
		}

	}
?>