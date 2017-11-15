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

  private function isAllow($path) {
    $urls = array_map(function($item) { return $item->url; }, $this->personnes_model->getAllowsPages($this->getUtilisateur()->id));
    foreach ($urls as $index => $url) {
      if (strpos($path, $url) !== false) {
        return true;
      }
    }
    return false;
  }

  public function menu() {
    $this->load->model('typeSouhait_model');
    $this->load->model('personnes_model');

    $buffer = "";
    if (!$this->isConnected()) {
      return $buffer;
    }

    $liens = array();
    $pour = array();
    foreach($this->typeSouhait_model->get() as $type) {
      $path = "/listeDeSouhaits/show/$type->pour/$type->type-$type->annee";
      if ($this->isAllow($path)) {
        if (!isset($pour[$type->libellePour])) {
          $pour[$type->libellePour] = array();
        }
        array_push($pour[$type->libellePour], array('label' => $type->libelleType, 'url' => site_url($path)));
      }
    }

    foreach($pour as $pour => $lien) {
      array_push($liens, array("submenu" => $lien, 'label' => $pour));
    }

    $navbar = "";
    if ($this->isAdmin()) {
      array_push($liens, array(
        'url' => site_url('/personnes'),
        'label' => 'Personnes'
      ));
      array_push($liens, array(
        'url' => site_url('/personnes/habilitations'),
        'label' => 'Habilitations'
      ));

      $navbar .= '<ul class="nav navbar-nav">';
      $navbar .= '<li><a data-plugin="navigationHistorique" href="#back" class="glyphicon glyphicon-chevron-left"></a></li>';
      $navbar .= '<li><a data-plugin="navigationHistorique" href="#forward" class="glyphicon glyphicon-chevron-right"></a></li>';
      $navbar .= "</ul>";
    }

    $buffer = '<ul class="nav navbar-nav">';
    foreach($liens as $index => $informations) {
      if (isset($informations['submenu'])) {
        $buffer .= '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $informations['label'] .' <span class="caret"></span></a>' . $this->showSubMenu($informations['submenu']) . '</li>';
      } else {
        $buffer .= '<li><a href="' . $informations['url'] . '">' . $informations['label'] . '</a></li>';
      }
    }
    $buffer .= "</ul>";

    echo $buffer . $navbar;
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