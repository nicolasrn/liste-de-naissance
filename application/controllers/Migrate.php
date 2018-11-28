<?php

class Migrate extends CI_Controller {
  public function __construct() {
    parent::__construct();
    $this->load->library('migration');
    $this->load->helper('cookie');
    $this->load->model('utilisateur_model');
  }

  public function index() {
    if ($this->utilisateur_model->isAdmin()) {
      $res = $this->migration->current();
      if ($res  === FALSE) {
        show_error($this->migration->error_string());
      } else {
        echo $res;
      }
    }
  }

  public function down($version) {
    if ($this->utilisateur_model->isAdmin()) {
      if ($version == "info") {
        var_dump($this->migration->find_migrations());
      } else {
        $this->load->library('migration');
        $res = $this->migration->version($version);
        if ($res === FALSE) {
          show_error($this->migration->error_string());
        } else {
          echo $res;
        }
      }
    }
  }
}

?>