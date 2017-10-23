<?php
  class TypeSouhait_model extends CI_Model {
    public function __construct() {
      $this->load->database();
    }

    public function get() {
      return $this->db->get_where('TTypeSouhait', array('actif' => 1))->result();
    }

  }
?>