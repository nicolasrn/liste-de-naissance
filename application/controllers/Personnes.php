<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('UserConnected.php');

class Personnes extends UserConnected {

  public function __construct() {
    parent::__construct();
    
    $this->load->library('parser');
    $this->load->model('personnes_model');
  }

  public function index($habilitations = null) {
    if ($habilitations != null) {
      if ($this->isAdmin()) {
        $groupes = $this->personnes_model->getGroupes();
        var_dump($groupes);
        $this->loadTemplate('habilitations', array('personnes' => $this->personnes_model->getGroupesAutorises(), 'groupes' => $groupes));
      } else {
        $this->redirectHome();
      }
    }
    else {
      if ($this->isAdmin()) {
        $this->loadTemplate('personnes', array('personnes' => $this->personnes_model->get()));
      } else {
        $this->redirectHome();
      }
    }
  }
}

?>