<?php
  class Personnes_model extends CI_Model {
    public function __construct() {
      $this->load->database();
    }

    public function get() {
      $query = $this->db->select('login, password, if(isMembreFamille=1, "oui", "non") as isMembreFamille')
                         ->from('TPersonne')
                         ->where('isAdmin', 0)
                         ->get();
      return $query->result_array();
    }
  }
?>