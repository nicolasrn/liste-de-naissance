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

  public function show($personne, $type, $etat = 0, $id = null) {
    $data = array(
      'etat' => $etat,
      'personne' => $personne, 
      'type' => $type, 
      'id' => $id);
    $data['urlWebService'] = site_url('/articles' . '/get' . '/' . $personne . '/' . $type . '/' . $etat);
    $data['menuAdministrationListeDeSouhaits'] = $this->menuAdmin($personne, $type);
    $this->loadPage($this->getPageSelonFamille(), $data);
  }

  protected function loadPage($page = 'home', $data = array()) {
    parent::loadPage($this->getPageSelonFamille(), $data);
  }

}

?>