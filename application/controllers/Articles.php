<?php

require_once('AbstractController.php');

class Articles extends AbstractController {

  public function __construct() {
    parent::__construct();
    $this->load->model('articles_model');
    $this->load->library('form_validation');
  }

  public function get($personne, $type, $etat) {
    $articles = $this->articles_model->getArticles($personne, $type, $etat);
    if ($this->isAdmin()) {
      $urlEdit = site_url("articles/edit/$personne/$type");
      foreach($articles as $article) {
        $article->libelleEdit = "<a href='" . $urlEdit . "/$article->id" . "'>" . $article->libelle . '</a>';
      }
    }
    $this->load->view('rest/json', array('resultat' => $articles));
  }

  public function detailReservation($personne, $type) {
    $reservations = $this->articles_model->getDetailReservations($personne, $type);
    $this->loadTemplate('detailDesReservations', array('reservations' => $reservations, 'menuAdministrationListeDeSouhaits' => $this->menuAdmin($personne, $type)));
  }

  public function edit($personne, $type, $id = null) {
    $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
    $this->form_validation->set_rules('libelle', 'Libellé de l\'article', 'required');
    $messages = [];
    if ($this->form_validation->run() === TRUE) {
      $action = $this->input->post('action');
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
    }
    $data = null;
    if ($id == null) {
      $data = array(
        'personne' => $personne, 
        'type' => $type, 
        'libelle' => '', 
        'etat' => $this->input->post('etat'),
        'quantiteSouhaitee' => 0, 
        'id' => $id,
        'urlEnregistrement' => current_url(),
        'ordrePrix' => 0.0,
        'lieu' => '',
        'url' => '',
        'libelleUrl' => '',
        'messages' => $messages
      );
    } else {
      $article = $this->articles_model->get($id);
      $data = array(
        'personne' => $personne, 
        'type' => $type, 
        'libelle' => $article->libelle, 
        'etat' => $article->etat,
        'quantiteSouhaitee' => $article->quantiteSouhaitee, 
        'id' => $article->id,
        'images' => array_chunk($article->img, 4),
        'urlEnregistrement' => current_url(),
        'ordrePrix' => $article->ordrePrix,
        'lieu' => $article->lieu,
        'url' => $article->url,
        'libelleUrl' => $article->libelleUrl,
      );
    }
    $this->load->model('etat_model');
    $options = "";
    foreach($this->etat_model->getAll() as $etat) {
      $options .=  "<option value='$etat->code'" . ($etat->code == $data['etat'] ? 'selected=selected': "") . "'>$etat->libelle</option>";
    }
    $data["optionsEtat"]  = $options;
    $data['menuAdministrationListeDeSouhaits'] = $this->menuAdmin($personne, $type);
    $this->loadPage('creerArticle', $data);
  }
}
?>