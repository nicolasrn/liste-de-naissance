<?php
  class Contenupage_model extends CI_Model {
    public function __construct() {
      $this->load->database();
    }

    public function get() {
      $query = $this->db->select('*')
                         ->from('TContenuPage')
                         ->get();
      return $query->result_array();
    }
  }
?>