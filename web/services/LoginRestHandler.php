<?php
	require_once("SimpleRest.php");

	class LoginRestHandler extends SimpleRest {

		public function __construct($action) {
			parent::__construct($action);

			$this->reqAuthentification = $this->bdd->prepare('select * from TPersonne where login = :login and password = :password');
			$this->reqAjoutUtilisateur = $this->bdd->prepare('insert into TPersonne (login, password) values(:login, :password)');
			$this->reqSelectPourAjoutUtilisateur = $this->bdd->prepare('select * from TPersonne where login = :login');
		}

		public function handleGet($get) {
			//var_dump($get);
			$reponse = null;
			$this->setHttpHeaders('application/json', 500);
			return $reponse;
		}

		public function handlePost($post) {
			$reponse = null;
			if (isset($_GET['action']) && $_GET['action'] == "enregistrement") {
				$reponse = $this->enregistrement($post['login'], $post['password'], $post['password2']);
				$this->setHttpHeaders('application/json', $reponse['code']);
				$reponse = $reponse['message'];
			} else {
				$reponse = $this->authentification($post["login"], $post["password"]);
				$this->setHttpHeaders('application/json', $reponse['code']);
				$reponse = json_encode($reponse['message'], JSON_FORCE_OBJECT);
			}
			return $reponse;
		}

		private function enregistrement($login, $password, $password2) {
			$res = null;
			if (empty($login)) {
				$res = array('message' => 'le login est vide', 'code' => 500);
			} else if (empty($password)  || empty($password2)) {
				$res = array('message' => 'les deux mots de passe ne peuvent être vide', 'code' => 500);
			}  else if ($password != $password2) {
				$res = array('message' => 'les deux mots de passe sont différents', 'code' => 500);
			} else if (!$this->isUtilisateurUnique($login)) {
				$res = array('message' => 'login déjà utilisé', 'code' => 500);
			} else {
				$res = $this->reqAjoutUtilisateur->execute(array(
					'login' => $login,
					'password' => $password
				));	
				$res = array('message' => $res, 'code' => 200);
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
			return array('message' => $response, 'code' => $code);
		}

	}
?>