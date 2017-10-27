<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_v1_TTypeSouhait2 extends CI_Migration {
  
  public function initialiserDonneesTable() {
    $this->db->insert('TTypeSouhait', array(
      'pour' => 'nous',
      'libellePour' => 'Nous',
      'type' => 'noel',
      'libelleType' => 'Nöel',
      'annee' => '2017',
      'actif' => 1
    ));
  }

  public function up() {
    $this->initialiserDonneesTable();
  }

  public function down() {
    $this->db->delete('TTypeSouhait', array("pour" => "nous", "type" => "noel", "annee" => "2017"));
  }
}
?>