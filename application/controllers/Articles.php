<?php

require_once('AbstractController.php');

class Articles extends AbstractController {

  public function __construct() {
    parent::__construct();
    $this->load->model('articles_model');
  }

  public function get($personne, $type, $etat) {
    $articles = $this->articles_model->getArticles($personne, $type, $etat);
    if ($this->isAdmin()) {
      $urlEdit = site_url('articles/edit/chloe/naissance');
      foreach($articles as $article) {
        $article->libelleEdit = "<a href='" . $urlEdit . "/$article->id" . "'>" . $article->libelle . '</a>';
      }
    }
    $this->load->view('rest/json', array('resultat' => $articles));
  }

  public function detailReservation($personne, $type) {
    $reservations = $this->articles_model->getDetailReservations($personne, $type);
    $this->loadTemplate('detailDesReservations', array('reservations' => $reservations));
  }

  public function edit($personne, $type, $id = null) {
    $action = $this->input->post('action');
    $messages = [];
    if ($action !== null) {
      switch($action) {
        case 'creerOuMaj' :
          $messages = $this->articles_model->enregistrerArticle($personne, $type, $id)['messages'];
          break;
        case 'supprimer' :
          $messages = $this->articles_model->supprimerArticle();
          break;
        case 'restaurer' :
          $messages = $this->articles_model->restaurerArticle();
          break;
      }
    }
    if ($id == null) {
      $data = array(
        'personne' => $personne, 
        'type' => $type, 
        'libelle' => '', 
        'quantiteSouhaitee' => 0, 
        'id' => $id,
        'urlEnregistrement' => current_url(),
        'messages' => $messages
      );
    } else {
      $article = $this->articles_model->get($id);
      $data = array(
        'personne' => $personne, 
        'type' => $type, 
        'libelle' => $article->libelle, 
        'quantiteSouhaitee' => $article->quantiteSouhaitee, 
        'id' => $article->id,
        'images' => array_chunk($article->img, 4),
        'urlEnregistrement' => current_url()
      );
    }
    $this->loadPage('creerArticle', $data);
  }
}
?>