<?php
require_once("SimpleRest.php");
		
class ArticlesRestHandler extends SimpleRest {
//var_dump()

	public function handleGet($get) {
		//var_dump($get);

		$reponse = $this->getAll();

		return json_encode($reponse, JSON_FORCE_OBJECT);
	}

	private function getAll() {
		try {
			$bdd = new PDO('mysql:host=localhost;dbname=liste-de-naissance;charset=utf8', 'client', 'client');
		} catch(Exception $e) {
			die('Erreur : '.$e->getMessage());
		}

		$req = $bdd->prepare('select * from tarticle');
		$req->execute();

		$resultats = [];
		$index = 0;
		while($donnees = $req->fetch()) {
			$resultats[$index++] = array (
				'id' => $donnees['id'],
				'libelle' => $donnees['libelle'],
				'quantiteSouhaite' => $donnees['quantiteSouhaitee'],
				'quantiteReserve' => $donnees['quantiteReservee'],
				'img' => $donnees['url']
			);
		}
		return $resultats;
	}

	public function handlePost($post) {
		var_dump($post);
	}

}
?>