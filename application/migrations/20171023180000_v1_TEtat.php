<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_v1_TEtat extends CI_Migration {
  public function creerTable() {
    $this->dbforge->add_field(array(
      'code' => array(
        'type' => 'int'
      ),
      'libelle' => array(
        'type' => 'varchar(255)'
      )
    ));
    $this->dbforge->add_key('code', TRUE);
    $this->dbforge->create_table('TEtat');
  }

  public function initialiserDonneesTable() {
    $this->db->insert('TEtat', array(
      'code' => 0,
      'libelle' => 'Actif'
    ));
    $this->db->insert('TEtat', array(
      'code' => 1,
      'libelle' => 'Supprime'
    ));
    $this->db->insert('TEtat', array(
      'code' => 2,
      'libelle' => 'Attente'
    ));
  }

  public function up() {
    $this->creerTable();
    $this->initialiserDonneesTable();
  }

  public function down() {
    $this->dbforge->drop_table('TEtat', true);
  }
}
?>