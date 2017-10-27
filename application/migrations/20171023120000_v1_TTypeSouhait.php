<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_v1_TTypeSouhait extends CI_Migration {
  public function creerTable() {
    $this->dbforge->add_field(array(
      'pour' => array(
        'type' => 'varchar(50)'
      ),
      'libellePour' => array(
        'type' => 'varchar(255)'
      ),
      'type' => array(
        'type' => 'varchar(50)'
      ),
      'libelleType' => array(
        'type' => 'varchar(255)'
      ),
      'annee' => array(
        'type' => 'varchar(4)'
      ),
      'actif' => array(
        'type' => 'int',
        'constraint' => 1,
      )
    ));
    $this->dbforge->add_key('pour', TRUE);
    $this->dbforge->add_key('type', TRUE);
    $this->dbforge->add_key('annee', TRUE);
    $this->dbforge->create_table('TTypeSouhait');
  }

  public function initialiserDonneesTable() {
    $this->db->insert('TTypeSouhait', array(
      'pour' => 'chloe',
      'libellePour' => 'Chloé',
      'type' => 'noel',
      'libelleType' => 'Nöel',
      'annee' => '2017',
      'actif' => 1
    ));
    $this->db->insert('TTypeSouhait', array(
      'pour' => 'chloe',
      'libellePour' => 'Chloé',
      'type' => 'naissance',
      'libelleType' => 'Liste de naissance',
      'annee' => '2017',
      'actif' => 1
    ));
    $this->db->insert('TTypeSouhait', array(
      'pour' => 'laurence',
      'libellePour' => 'Laurence',
      'type' => 'noel',
      'libelleType' => 'Nöel',
      'annee' => '2017',
      'actif' => 1
    ));
    $this->db->insert('TTypeSouhait', array(
      'pour' => 'nicolas',
      'libellePour' => 'Nicolas',
      'type' => 'noel',
      'libelleType' => 'Nöel',
      'annee' => '2017',
      'actif' => 1
    ));
  }

  public function migrationDonneesExistantes() {
    $this->db->set('type', 'naissance-2017');
    $this->db->where('type', 'naissance');
    $this->db->update("TArticle");

    $this->db->set('path', '#chloe.naissance-2017');
    $this->db->where('path', '#chloe.naissance');
    $this->db->update("TContenuPage");
  }

  public function up() {
    $this->creerTable();
    $this->initialiserDonneesTable();
    $this->migrationDonneesExistantes();
  }

  public function down() {
    $this->dbforge->drop_table('TTypeSouhait', true);
    $this->db->where('type', "naissance-2017");
    $this->db->update('TArticle', array(
      'type' => "naissance-2017"
    ));
    $this->db->where('path', "#chloe.naissance");
    $this->db->update('TContenuPage', array(
      'path' => "#chloe.naissance-2017"
    ));
  }
}
?>