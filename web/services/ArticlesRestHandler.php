<?php
	require_once("SimpleRest.php");
			
	class ArticlesRestHandler extends SimpleRest {
		private $reqArticles;
		private $reqArticleParUtilisateur;
		private $reqImage;
		private $reqUpdate;
		private $reqSelectForUpdate;
		private $reqInsert;
		private $reqInsertArticle;
		private $reqInsertImages;
		private $siteRoot;

		public function __construct($action) {
			parent::__construct($action);
			$this->siteRoot = realpath(dirname(__FILE__)) . '/../..';

			$this->reqArticles = $this->bdd->prepare('select id, libelle, COALESCE(quantiteSouhaitee, 0) as quantiteSouhaitee, COALESCE(sum(quantiteReservee), 0) as quantiteReserveeTotale from TArticle a left join PersonneReserveCadeau prc on prc.idArticle = a.id group by a.id, a.libelle, a.quantiteSouhaitee');
			$this->reqArticleParUtilisateur = $this->bdd->prepare('select prc.quantiteReservee from TPersonne p join PersonneReserveCadeau prc on p.id = prc.idPersonne and prc.idArticle = :idArticle where p.id = :idPersonne');
			$this->reqImage = $this->bdd->prepare('select src from TImage where idArticle = :idArticle');

			$this->reqSelectForUpdate = $this->bdd->prepare('select * from PersonneReserveCadeau where idArticle = :idArticle and idPersonne = :idPersonne');
			$this->reqUpdate = $this->bdd->prepare('update PersonneReserveCadeau set quantiteReservee = :valeur where idArticle = :idArticle and idPersonne = :idPersonne');
			$this->reqInsert = $this->bdd->prepare('insert into PersonneReserveCadeau (idArticle, idPersonne, quantiteReservee) values (:idArticle, :idPersonne, :valeur)');
			$this->reqInsertArticle = $this->bdd->prepare('insert into TArticle (libelle, quantiteSouhaitee) values (:libelle, :quantiteSouhaitee)');
			$this->reqInsertImages = $this->bdd->prepare('insert into TImage (src, idArticle) values (:src, :idArticle)');
		}

		public function handleGet($get) {
			//var_dump($get);
			$reponse = $this->getAll($get);

			$this->setHttpHeaders('application/json', 200);
			return json_encode($reponse, JSON_FORCE_OBJECT);
		}

		private function getAll($get) {
			$this->reqArticles->execute();

			$resultats = array();
			$index = 0;
			while($donnees = $this->reqArticles->fetch()) {
				$resultats[$index++] = array (
					'id' => $donnees['id'],
					'libelle' => $donnees['libelle'],
					'quantiteSouhaitee' => $donnees['quantiteSouhaitee'],
					'quantiteReserveeTotal' => $donnees['quantiteReserveeTotale'],
					'quantiteReserveeUtilisateur' => $this->getQuantiteReserveeUtilisateur($donnees['id'], $get['idUser']),
					'img' => $this->getImages($donnees['id'])
				);
			}
			return $resultats;
		}

		public function getImages($id) {
			$this->reqImage->execute(array(
				'idArticle' => $id
			));
			$images = array();
			while($donnees = $this->reqImage->fetch()) {
				$images[] = $donnees['src'];
			}
			return $images;
		}

		public function getQuantiteReserveeUtilisateur($idArticle, $idUser) {
			$this->reqArticleParUtilisateur->execute(array(
				'idArticle' => $idArticle,
				'idPersonne' => $idUser
			));
			$qte = 0;
			while($donnees = $this->reqArticleParUtilisateur->fetch()) {
				$qte = $donnees['quantiteReservee'];
			}
			return $qte;
		}

		private function isUtilisateurADejaReserve($idUser, $idArticle) {
			$this->reqSelectForUpdate->execute(array(
				'idPersonne' => $idUser,
				'idArticle' => $idArticle,
			));
			return $this->reqSelectForUpdate->rowCount() == 1;
		}

		public function handlePost($post) {
			$res = null;
			if (isset($post['action']) && $post['action'] == 'addArticle') {
				$resImages = $this->enregristrerArticle($post);
				if (!isset($resImages['idArticle'])) {
					$resImages['idArticle'] = -1;
				}
				$res = json_encode(array('id' => $resImages['idArticle'] , 'message' => $resImages['message']), JSON_FORCE_OBJECT);
				$this->setHttpHeaders('application/json', $resImages['idArticle'] > -1 ? 200 : 500);
			} else if (isset($post['action']) && $post['action'] == 'updateReservation') {
				$idUser = $post['idUser'];
				$idArticle = $post['idArticle'];
				$newValue = $post['newValue'];

				if ($this->isUtilisateurADejaReserve($idUser, $idArticle)) {
					$res = $this->reqUpdate->execute(array(
						'idArticle' => $idArticle,
						'idPersonne' => $idUser,
						'valeur' => $newValue
					));	
				} else {
					$res = $this->reqInsert->execute(array(
						'idArticle' => $idArticle,
						'idPersonne' => $idUser,
						'valeur' => $newValue
					));
				}
				$this->setHttpHeaders('application/json', $res ? 200 : 500);
			}
			return $res;
		}

		public function enregristrerArticle($post) {
			$resImages = $this->handleUploadFile($_FILES);
			if ($resImages['isSuccess']) {
				$this->reqInsertArticle->execute(array(
					'libelle' => $post['libelle'], 
					'quantiteSouhaitee' => $post['quantiteSouhaitee']
				));
				$idArticle = $this->bdd->lastInsertId();
				$resImages['idArticle'] = $idArticle;
				$resImages = $this->enregristrerImage($resImages, $idArticle);
			}
			return $resImages;
		}

		public function enregristrerImage($resImages, $idArticle) {
			foreach ($resImages['paths'] as $path) {
				$path = str_replace($this->siteRoot, '', $path);
				$res = $this->reqInsertImages->execute(array(
					'src' => $path,
					'idArticle' => $idArticle
				));
			}
			
			return $resImages;
		}

		public function handleUploadFile($file) {
			$target_dir = $this->siteRoot . "/img/";
			$message = array();
			$paths = array();
			$isSuccess = true;
			$nbImage = 0;

			foreach ($file as $fileId => $fileData) {
				$fileName = $_FILES[$fileId]["name"];
				if (!empty($fileName)) {
					$nbImage++;
					$target_file = $target_dir . basename($fileName);

					$uploadOk = true;
					$check = getimagesize($_FILES[$fileId]["tmp_name"]);
					if($check !== false) {
						$uploadOk = true;
					} else {
						$uploadOk = false;
						$isSuccess = false;
						$message[] = "$fileName - n'est pas une image";
					}
					// Check if $uploadOk is set to 0 by an error
					if ($uploadOk) {
						if (move_uploaded_file($_FILES[$fileId]["tmp_name"], $target_file)) {
							$message[] = "$fileName - à bien été téléchargé";
							$paths[] = $target_file;
						}
					}
				}
			}

			if ($nbImage > 0) {
				return array('message' => $message, 'paths' => $paths, 'isSuccess' => $isSuccess);
			}

			return array('message' => "aucune image n'a été trouvée", 'paths' => $paths, 'isSuccess' => false); 
		}
	}
?>