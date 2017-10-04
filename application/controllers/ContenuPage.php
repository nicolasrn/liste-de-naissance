<?php

require_once('AbstractController.php');

class ContenuPage extends AbstractController {

  public function __construct() {
    parent::__construct();
    $this->load->model('contenupage_model');
  }

  public function index() {
    $this->load->view('rest/json', array('resultat' => $this->contenupage_model->get()));
  }

}
?>