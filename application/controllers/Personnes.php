<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('UserConnected.php');

class Personnes extends UserConnected {

  public function __construct() {
    parent::__construct();
    
    $this->load->library('parser');
    $this->load->model('personnes_model');
  }

  public function index() {
    if ($this->isAdmin()) {
      $this->loadTemplate('personnes', array('personnes' => $this->getDetailToutesPersonnes()));
    } else {
      $this->redirectHome();
    }
  }

  private function getDetailToutesPersonnes() {
    return $this->personnes_model->get();
  }
}

?>