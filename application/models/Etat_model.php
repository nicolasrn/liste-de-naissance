<?php
  class Etat_model extends CI_Model {
    public function __construct() {
      $this->load->database();
    }

    public function getAll() {
      return $this->db->get('TEtat')->result();
    }

  }
?>