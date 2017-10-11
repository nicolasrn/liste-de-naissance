<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('UserConnected.php');

class ListeDeSouhaits extends UserConnected {

  public function __construct() {
    parent::__construct();
  }

  public function index() {
    $this->loadPage($this->getPageSelonFamille());
  }

  private function getPageSelonFamille() {
    if ($this->isMembreFamille()) {
      return 'liste-de-naissance-famille';
    } else {
      return 'liste-de-naissance';
    }
  }

  public function show($personne, $type, $id = null) {
    $data = array(
      'etat' => 0,
      'personne' => $personne, 
      'type' => $type, 
      'id' => $id);
    $data['urlWebService'] = site_url('/articles' . '/get' . '/' . $personne . '/' . $type . '/0');
    $data['menuAdministrationListeDeSouhaits'] = $this->menuAdmin($personne, $type);
    $this->loadPage($this->getPageSelonFamille(), $data);
  }

  protected function loadPage($page = 'home', $data = array()) {
    parent::loadPage($this->getPageSelonFamille(), $data);
  }

  private function menuAdmin($personne, $type) {
    $urlAjouterArticle = site_url('/articles' . '/edit' . '/' . $personne . '/' . $type);
    $urlDetailReservation = site_url('/articles' . '/detailReservation' . '/' . $personne . '/' . $type);
    
    return $this->isAdmin() ? '
      <ul class="nav nav-tabs">
        <li><a href="' . $urlAjouterArticle . '">Ajouter un article</a></li>
        <li><a href="' . $urlDetailReservation . '">Voir le détail des réservations</a></li>
      </ul>' : '';
  }

}

?>