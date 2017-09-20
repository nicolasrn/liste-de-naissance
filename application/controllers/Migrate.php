<?php

class Migrate extends CI_Controller {
  public function index() {
    $this->load->library('migration');
    $res = $this->migration->current();
    if ($res  === FALSE) {
      show_error($this->migration->error_string());
    } else {
      echo $res;
    }
  }

  public function down() {
    $this->load->library('migration');
    $this->migration->version(0);
  }
}

?>