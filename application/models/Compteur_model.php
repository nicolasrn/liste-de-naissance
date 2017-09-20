<?php
  class Compteur_model extends CI_Model {
    public function __construct() {
      $this->load->database();
    }

    public function update() {
      $sql = 'insert into PersonneReserveCadeau (idArticle, idPersonne, quantiteReservee) values (?, ?, ?) 
              ON DUPLICATE KEY UPDATE idArticle=VALUES(idArticle),  idPersonne=VALUES(idPersonne),  quantiteReservee=VALUES(quantiteReservee)';

      $query = $this->db->query($sql, array(
        $this->input->post('idArticle'), 
        $this->input->post('idPersonne'), 
        $this->input->post('newValue')
      ));
    }

  }
?>