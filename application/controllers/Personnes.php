<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('UserConnected.php');

class Personnes extends UserConnected {

  public function __construct() {
    parent::__construct();
    
    $this->load->library('parser');
    $this->load->model('personnes_model');
    $this->load->model('typeSouhait_model');
    
  }

  public function update($habilitation = null) {
    if ($habilitation != null) {
      $this->personnes_model->updateHabilitations();
    }
  }

  public function index($habilitations = null) {
    if ($this->isAdmin()) {
      $this->loadTemplate('personnes', array('personnes' => $this->personnes_model->get()));
    } else {
      $this->redirectHome();
    }
  }

  public function habilitations() {
    if ($this->isAdmin()) {
      $this->loadTemplate('habilitations', array('personnes' => $this->personnes_model->getGroupesAutorises(), 'groupes' => $this->personnes_model->getGroupes()));
    } else {
      $this->redirectHome();
    }
  }

  public function reservations() {
    if ($this->isAdmin()) {
      $this->loadTemplate('reservations', array(
        'reservations' => $this->personnes_model->getReservations(), 
        'personnes' => $this->personnes_model->getAllUtilisateurs(),
        'types' => $this->typeSouhait_model->get())
      );
      
    } else {
      $this->redirectHome();
    }
  }
}

?>