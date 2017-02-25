<?php
	require_once("SimpleRest.php");
	require_once("../lienEdit.php");
			
	class ArticlesRestHandler extends SimpleRest {
		private $reqArticles;
		private $reqArticle;
		private $reqArticleParUtilisateur;
		private $reqImage;
		private $reqUpdate;
		private $reqSelectForUpdate;
		private $reqInsert;
		private $reqInsertArticle;
		private $reqInsertImages;
		private $reqUpdateArticle;
		private $reqArticleReserve;
		private $siteRoot;

		public function __construct($action) {
			parent::__construct($action);
			$this->siteRoot = realpath(dirname(__FILE__)) . '/../..';
			$this->reqArticles = $this->bdd->prepare(
				'
				select * 
				from (
					SELECT a.id, a.libelle, COALESCE(a.quantiteSouhaitee, 0) quantiteSouhaitee, COALESCE(sum(r.quantiteReservee), 0) as quantiteReserveeTotale
					FROM TArticle a
					left join PersonneReserveCadeau r on a.id = r.idArticle and quantiteReservee > 0
					group by a.id, libelle, quantiteSouhaitee
					having COALESCE(a.quantiteSouhaitee, 0) > COALESCE(sum(r.quantiteReservee), 0)
					order by lower(a.libelle) asc
				) a union 
				select * 
				from (
					SELECT a.id, a.libelle, COALESCE(a.quantiteSouhaitee, 0) quantiteSouhaitee, COALESCE(sum(r.quantiteReservee), 0) as quantiteReserveeTotale
					FROM TArticle a
					left join PersonneReserveCadeau r on a.id = r.idArticle and quantiteReservee > 0
					group by a.id, libelle, quantiteSouhaitee
					having COALESCE(a.quantiteSouhaitee, 0) = COALESCE(sum(r.quantiteReservee), 0)
					order by lower(a.libelle) asc
				) b
				'
			);
			$this->reqArticleParUtilisateur = $this->bdd->prepare('select prc.quantiteReservee from TPersonne p join PersonneReserveCadeau prc on p.id = prc.idPersonne and prc.idArticle = :idArticle where p.id = :idPersonne');
			$this->reqArticle = $this->bdd->prepare('select id, libelle, quantiteSouhaitee from TArticle a where a.id = :idArticle');
			$this->reqImage = $this->bdd->prepare('select id, src from TImage where idArticle = :idArticle');
			$this->reqArticleReserve = $this->bdd->prepare(
				'
				SELECT a.libelle, r.quantiteReservee, p.login
				FROM TArticle a
				join PersonneReserveCadeau r on a.id = r.idArticle and r.quantiteReservee > 0
				join TPersonne p on p.id = r.idPersonne
				order by p.login, r.quantiteReservee
				'
			);

			$this->reqSelectForUpdate = $this->bdd->prepare('select * from PersonneReserveCadeau where idArticle = :idArticle and idPersonne = :idPersonne');
			$this->reqUpdate = $this->bdd->prepare('update PersonneReserveCadeau set quantiteReservee = :valeur where idArticle = :idArticle and idPersonne = :idPersonne');
			$this->reqInsert = $this->bdd->prepare('insert into PersonneReserveCadeau (idArticle, idPersonne, quantiteReservee) values (:idArticle, :idPersonne, :valeur)');
			$this->reqInsertArticle = $this->bdd->prepare('insert into TArticle (libelle, quantiteSouhaitee) values (:libelle, :quantiteSouhaitee)');
			$this->reqInsertImages = $this->bdd->prepare('insert into TImage (src, idArticle) values (:src, :idArticle)');

			$this->reqUpdateArticle = $this->bdd->prepare('update TArticle set quantiteSouhaitee = :quantiteSouhaitee where id = :id');
			$this->reqDeleteImage = $this->bdd->prepare('delete from TImage where id = :id');
		}

		public function handleGet($get) {
			//var_dump($get);
			$reponse = null;
			$code = 200;
			if (isset($get['action'])) {
				if ($get['action'] == 'getAll') {
					$reponse = $this->getAll($get);
					$reponse = json_encode($reponse, JSON_FORCE_OBJECT);
				} else if ($get['action'] == 'get') {
					$reponse = $this->get($get);
					$reponse = json_encode($reponse, JSON_FORCE_OBJECT);
				} else if ($get['action'] == 'articleReserve') {
					$reponse = $this->getArticleReserve($get);
					$reponse = json_encode($reponse);
				} else {
					$code = 500;
				}
			} else {
				$code = 500;
			}

			$this->setHttpHeaders('application/json', $code);
			return $reponse;
		}

		private function get($get) {
			$this->reqArticle->execute(array('idArticle' => $get['id']));

			$resultat = null;
			while($donnees = $this->reqArticle->fetch()) {
				$resultat = array (
					'id' => $donnees['id'],
					'libelle' => $donnees['libelle'],
					'quantiteSouhaitee' => $donnees['quantiteSouhaitee'],
					'img' => $this->getImages($donnees['id'])
				);
			}
			return $resultat;
		}

		private function getAll($get) {
			$this->reqArticles->execute();

			$resultats = array();
			$index = 0;
			while($donnees = $this->reqArticles->fetch()) {
				$resultats[$index++] = array (
					'id' => $donnees['id'],
					'libelle' => getLienEdition($donnees['id'], $donnees['libelle']),
					'quantiteSouhaitee' => $donnees['quantiteSouhaitee'],
					'quantiteReserveeTotal' => $donnees['quantiteReserveeTotale'],
					'quantiteReserveeUtilisateur' => $this->getQuantiteReserveeUtilisateur($donnees['id'], $get['idUser']),
					'img' => $this->getImages($donnees['id'])
				);
			}
			return $resultats;
		}

		private function getArticleReserve ($get) {
			$this->reqArticleReserve->execute();

			$resultat = array();
			while($donnees = $this->reqArticleReserve->fetch()) {
				array_push($resultat, array (
					'article' => $donnees['libelle'],
					'quantiteReservee' => $donnees['quantiteReservee'],
					'login' => $donnees['login']
				));
			}
			return $resultat;
		}

		private function getImages($id) {
			$this->reqImage->execute(array(
				'idArticle' => $id
			));
			$images = array();
			while($donnees = $this->reqImage->fetch()) {
				$images[] = array('src' => $donnees['src'], 'id' => $donnees['id']);
			}
			return $images;
		}

		private function getQuantiteReserveeUtilisateur($idArticle, $idUser) {
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
				if (!isset($post['idArticle']) || empty($post['idArticle'])) {
					$post['idArticle'] = intval(-1);
				}
				$resImages = $this->enregristrerArticle($post);
				$res = json_encode(array('id' => $resImages['idArticle'] , 'message' => $resImages['message']), JSON_FORCE_OBJECT);
				$this->setHttpHeaders('application/json', $resImages['isSuccess'] ? 200 : 500);
			} else if (isset($post['action']) && $post['action'] == 'updateArticle') {
				$resImages = $this->updateArticle($post);
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

		private function updateArticle($post) {
			$this->bdd->beginTransaction();
			$res = $this->enregristrerArticle($post);
			if ($res['isSuccess'] == false) {
				$this->bdd->rollBack();
			} else {
				$this->bdd->commit();
			}
			return $res;
		}

		private function enregristrerArticle($post) {
			$resImages = $this->handleUploadFile($_FILES, $post);
			$idArticle = $post['idArticle'];
			if ($resImages['isSuccess']) {
				if ($post['idArticle'] == -1) {
					$this->reqInsertArticle->execute(array(
						'libelle' => $post['libelle'], 
						'quantiteSouhaitee' => $post['quantiteSouhaitee']
					));
					$idArticle = $this->bdd->lastInsertId();
				} else {
					$aSupprimer = explode(';', $post['toDelete']);
					foreach ($aSupprimer as $key => $value) {
						$res = $this->reqDeleteImage->execute(array('id' => $value));
					}
					$res = $this->reqUpdateArticle->execute(array('quantiteSouhaitee' => $post['quantiteSouhaitee'], 'id' => $idArticle));
				} 
				$resImages = $this->enregristrerImage($resImages, $idArticle);
			}
			return $resImages;
		}

		private function enregristrerImage($resImages, $idArticle) {
			foreach ($resImages['paths'] as $path) {
				$path = str_replace($this->siteRoot, '', $path);
				$res = $this->reqInsertImages->execute(array(
					'src' => $path,
					'idArticle' => $idArticle
				));
			}
			
			return $resImages;
		}

		private function handleUploadFile($file, $post) {
			$images = $this->getImages($post['idArticle']);
			$target_dir = $this->siteRoot . "/img/";
			$message = array();
			$paths = array();
			$isSuccess = count($images);
			$nbImageUploade = 0;

			foreach ($file as $fileId => $fileData) {
				$fileName = $_FILES[$fileId]["name"];

				if (!empty($fileName)) {
					$nbImageUploade++;
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

			$res = array('message' => $message, 'paths' => $paths, 'isSuccess' => (($isSuccess + $nbImageUploade) > 0), 'idArticle' => $post['idArticle']);
			return $res; 
		}
	}
?>