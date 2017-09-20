<?php

abstract class AbstractController extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->helper('cookie');
    $this->load->model('utilisateur_model');
  }

  protected function loadPage($page = 'home', $data = array()) {
    $data = array_merge(array(
      'etat' => 0,
      'personne' => 'chloe', 
      'type' => 'naissance', 
      'id' => null), $data);
    $data['urlWebService'] = site_url('/articles' . '/get' . '/' . $data['personne'] . '/' . $data['type'] . '/' . $data['etat']);

    $this->load->view('templates/header', $data);
    if ($this->isConnected()) {
      $this->load->view("pages/$page"."_auth", $data);
    } else {
      $this->load->view("pages/$page", $data);
    }
    $this->load->view('templates/footer', $data);
  }

  protected function loadTemplate($page, $data = array()) {
    $this->load->library('parser');
    $this->load->view('templates/header', $data);
    if ($this->isConnected()) {
      $this->parser->parse("pages/$page"."_auth", $data);
    }
    $this->load->view('templates/footer', $data);
  }

  protected function isConnected() {
    return get_cookie("utilisateur") !== null;
  }

  protected function isMembreFamille() {
    return $this->utilisateur_model->isMembreFamille();
  }

  protected function isAdmin() {
    return $this->utilisateur_model->isAdmin();
  }

}

?>