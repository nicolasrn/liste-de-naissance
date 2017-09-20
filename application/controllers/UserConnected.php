<?php

require_once('AbstractController.php');

abstract class UserConnected extends AbstractController {

  public function __construct() {
    parent::__construct();
    $this->load->library('session');
  }

  public function connexion() {
    $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
    $this->form_validation->set_rules('login', 'Login', 'required');
    $this->form_validation->set_rules('password', 'Mot de passe', 'required');

    if ($this->form_validation->run() === FALSE) {
      $this->loadPage();
    } else {
      $utilisateur = $this->utilisateur_model->get_utilisateur($this->input->post('login'), $this->input->post('password'));
      if ($utilisateur != null) {
        set_cookie('utilisateur', json_encode($utilisateur), 3600 * 24 * 15);
        //redirect(site_url()."/listedesouhaits/show/chloe/naissance");//redirect(site_url() . $this->utilisateur_model->get_utilisateur->getPageParDefaut());
        redirect(site_url());
      } else {
        $this->loadPage('home', array('erreur' => "le login ou le mot de passe est incorrecte"));
      }
    }
  }

  public function deconnexion() {
    delete_cookie('utilisateur');
    $this->redirectHome();
  }

  protected function redirectHome() {
    redirect(site_url()."/home");
  }

}

?>