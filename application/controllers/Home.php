<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('UserConnected.php');

class Home extends UserConnected {

  public function __construct() {
    parent::__construct();

    $this->load->library('form_validation');
  }

  public function index() {
    $this->loadPage();
  }

  public function inscription() {
    $this->loadPage('inscription');
  }

  public function enregistrement() {
    $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
    $this->form_validation->set_rules('login', 'Login', 'required|callback_verificationLoginUnique');
    $this->form_validation->set_rules('password', 'Mot de passe', 'required');
    $this->form_validation->set_rules('password2', 'Confirmation du mot de passe', 'required|matches[password]');
    $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_verificationEmailUnique');
    $this->form_validation->set_message('verificationLoginUnique', 'le login existe déjà');
    $this->form_validation->set_message('verificationEmailUnique', 'l\'adresse email existe déjà');

    if ($this->form_validation->run() === TRUE) {
      $this->utilisateur_model->enregistrerUtilisateur();
      $this->session->set_flashdata('enregistrementOk', true);
      redirect(site_url()."/home");
    } else {
      redirect(site_url()."/home/inscription");
    }
  }

  public function menu() {
    $buffer = "";
    if (!$this->isConnected() || !$this->isMembreFamille()) {
      return $buffer;
    }
    $liens = array();
    array_push($liens, array(
      'submenu' => array(
        array('label' => 'liste de naissance', 'url' => site_url('/listeDeSouhaits/show/chloe/naissance')),
        array('label' => 'anniversaire', 'url' => site_url('/listeDeSouhaits/show/chloe/cadeau'))
      ),
      'label' => 'Chloé'
    ));
    array_push($liens, array(
      'submenu' => array(
        array('label' => 'anniversaire', 'url' => site_url('/listeDeSouhaits/show/laurence/cadeau'))
      ),
      'label' => 'Laurence'
    ));
    array_push($liens, array(
      'submenu' => array(
        array('label' => 'anniversaire', 'url' => site_url('/listeDeSouhaits/show/nicolas/cadeau'))
      ),
      'label' => 'Nicolas'
    ));

    if ($this->isAdmin()) {
      array_push($liens, array(
        'url' => site_url('/personnes'),
        'label' => 'Personnes'
      ));
    }

    $buffer = '<ul class="nav navbar-nav">';
    foreach($liens as $index => $informations) {
      if (isset($informations['submenu'])) {
        $buffer = $buffer . '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $informations['label'] .' <span class="caret"></span></a>' . $this->showSubMenu($informations['submenu']) . '</li>';
      } else {
        $buffer = $buffer . '<li><a href="' . $informations['url'] . '">' . $informations['label'] . '</a></li>';
      }
    }
    $buffer = $buffer . "</ul>";

    $buffer .= '<ul class="nav navbar-nav">';
    $buffer .= '<li><a data-plugin="navigationHistorique" href="#back" class="glyphicon glyphicon-chevron-left"></a></li>';
    $buffer .= '<li><a data-plugin="navigationHistorique" href="#forward" class="glyphicon glyphicon-chevron-right"></a></li>';
    $buffer = $buffer . "</ul>";
    echo $buffer;
  }

  private function showSubMenu($submenu) {
    $buffer = '<ul class="dropdown-menu">';
    foreach($submenu as $index => $informations) {
      $buffer = $buffer . '<li><a href="' . $informations['url'] . '">' . $informations['label'] . '</a></li>';
    }
    $buffer = $buffer . "</ul>";
    return $buffer;
  }

  public function verificationLoginUnique($login) {
    return $this->utilisateur_model->verificationLoginUnique($login);
  }

  public function verificationEmailUnique($email) {
    //$this->utilisateur_model->verificationEmailUnique($email);
    return true;
  }
}

?>