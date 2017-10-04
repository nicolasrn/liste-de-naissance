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
    $data['urlAjouterArticle'] = site_url('/articles' . '/edit' . '/' . $data['personne'] . '/' . $data['type']);
    $data['urlDetailReservation'] = site_url('/articles' . '/detailReservation' . '/' . $data['personne'] . '/' . $data['type']);
    $data['urlWebService'] = site_url('/articles' . '/get' . '/' . $data['personne'] . '/' . $data['type'] . '/' . $data['etat']);
    $this->loadPage($this->getPageSelonFamille(), $data);
  }

  protected function loadPage($page = 'home', $data = array()) {
    parent::loadPage($this->getPageSelonFamille(), $data);
  }

}

?>