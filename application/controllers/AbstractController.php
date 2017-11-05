<?php

abstract class AbstractController extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->helper('cookie');
    $this->load->model('utilisateur_model');
  }

  protected function loadPage($page = 'home', $data = array()) {
    $data = array_merge($this->getDataParDefaut(), $data);
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

  protected function menuAdmin($personne, $type) {
    $data = array(
      "Ajouter un article" => site_url('/articles' . '/edit' . '/' . $personne . '/' . $type),
      "Voir les articles" => site_url("/listeDeSouhaits/show/$personne/$type"),
      "Voir les articles supprimés" => site_url("/listeDeSouhaits/show/$personne/$type/1"),
      "Voir les articles en attente" => site_url("/listeDeSouhaits/show/$personne/$type/2"),
      "Voir le détail des réservations" => site_url('/articles' . '/detailReservation' . '/' . $personne . '/' . $type)
    );

    $buffer = "";
    foreach ($data as $libelle => $lien) {
      if ($this->isActive($lien)) {
        $buffer .= '<li class="active"><a href="#">' . $libelle . '</a></li>';
      } else {
        $buffer .= '<li><a href="' . $lien . '">' . $libelle . '</a></li>';
      }
    }

    return $this->isAdmin() ? '<ul class="nav nav-tabs nav-justified">' . $buffer . '</ul>' : '';
  }

  private function isActive($url) {
    return site_url($this->uri->uri_string()) == $url ? "active" : "";
  }

  private function getDataParDefaut() {
    if ($this->isMembreFamille()) {
      return array(
        'etat' => 0,
        'personne' => 'chloe', 
        'type' => 'noel-2017', 
        'id' => null); 
    }
    return array(
      'etat' => 0,
      'personne' => 'chloe', 
      'type' => 'naissance-2017', 
      'id' => null);
  }
}

?>