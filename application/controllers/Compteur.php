<?php

require_once('AbstractController.php');

class Compteur extends AbstractController {

  public function __construct() {
    parent::__construct();
    $this->load->model('compteur_model');
  }

  public function index() {
    $this->compteur_model->update();
    //$this->load->view('rest/json', array('resultat' => $this->liste_model->getListe()));
  }

}
?>