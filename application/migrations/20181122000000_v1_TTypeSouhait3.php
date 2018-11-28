<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_v1_TTypeSouhait3 extends CI_Migration {
  
  public function initialiserDonneesTable() {
    $this->db->set("actif", 0);
    $this->db->where('type', 'noel');
    $this->db->where('annee', '2017');
    $this->db->update("TTypeSouhait");
  
    $this->db->insert('TTypeSouhait', array(
      'pour' => 'nous',
      'libellePour' => 'Nous',
      'type' => 'noel',
      'libelleType' => 'Nöel',
      'annee' => '2018',
      'actif' => 1
    ));
    $this->db->insert('TTypeSouhait', array(
      'pour' => 'nicolas',
      'libellePour' => 'Nicolas',
      'type' => 'noel',
      'libelleType' => 'Nöel',
      'annee' => '2018',
      'actif' => 1
    ));
    $this->db->insert('TTypeSouhait', array(
      'pour' => 'laurence',
      'libellePour' => 'Laurence',
      'type' => 'noel',
      'libelleType' => 'Nöel',
      'annee' => '2018',
      'actif' => 1
    ));
    $this->db->insert('TTypeSouhait', array(
      'pour' => 'chloe',
      'libellePour' => 'Chloé',
      'type' => 'noel',
      'libelleType' => 'Nöel',
      'annee' => '2018',
      'actif' => 1
    ));
  }

  public function up() {
    $this->initialiserDonneesTable();
  }

  public function down() {
    $this->db->delete('TTypeSouhait', array("pour" => "nous", "type" => "noel", "annee" => "2018"));
    $this->db->delete('TTypeSouhait', array("pour" => "nicolas", "type" => "noel", "annee" => "2018"));
    $this->db->delete('TTypeSouhait', array("pour" => "laurence", "type" => "noel", "annee" => "2018"));
    $this->db->delete('TTypeSouhait', array("pour" => "chloe", "type" => "noel", "annee" => "2018"));
    
    $this->db->set("actif", 1);
    $this->db->where('type', 'noel');
    $this->db->where('annee', '2017');
    $this->db->update("TTypeSouhait");
  }
}
?>