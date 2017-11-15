<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_v1_THabilitations extends CI_Migration {
  public function creerTable() {
    $this->dbforge->add_field(array(
      'idPersonne' => array(
        'type' => 'int'
      ),
      'idGroupe' => array(
        'type' => 'int'
      )
    ));
    $this->dbforge->add_key('idPersonne', TRUE);
    $this->dbforge->add_key('idGroupe', TRUE);
    $this->dbforge->create_table('PersonneAppartientGroupe');

    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'int',
        'auto_increment' => TRUE
      ),
      'libelleGroupe' => array(
        'type' => 'varchar(50)'
      )
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('TGroupe');

    $this->dbforge->add_field(array(
      'idGroupe' => array(
        'type' => 'int'
      ),
      'idPage' => array(
        'type' => 'int'
      )
    ));
    $this->dbforge->add_key('idGroupe', TRUE);
    $this->dbforge->add_key('idPage', TRUE);
    $this->dbforge->create_table('GroupeAccedePage');

    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'int',
        'auto_increment' => TRUE
      ),
      'url' => array(
        'type' => 'varchar(255)'
      )
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('TPage');
  }

  public function initialiserDonneesTable() {
    $this->db->insert('TGroupe', array('id' => '1', 'libelleGroupe' => 'lambda'));
    $this->db->insert('TGroupe', array('id' => '2', 'libelleGroupe' => 'membreFamille'));
    $this->db->insert('TGroupe', array('id' => '3', 'libelleGroupe' => 'membreFamilleProche'));

    $this->db->insert('TPage', array('id' => '1', 'url' => '/home'));
    $this->db->insert('TPage', array('id' => '2', 'url' => '/listeDeSouhaits/show/chloe'));
    $this->db->insert('TPage', array('id' => '3', 'url' => '/listeDeSouhaits/show/nicolas'));
    $this->db->insert('TPage', array('id' => '4', 'url' => '/listeDeSouhaits/show/laurence'));

    $this->db->insert('GroupeAccedePage', array('idGroupe' => '1', 'idPage' => '1'));
    $this->db->insert('GroupeAccedePage', array('idGroupe' => '2', 'idPage' => '1'));
    $this->db->insert('GroupeAccedePage', array('idGroupe' => '2', 'idPage' => '2'));
    $this->db->insert('GroupeAccedePage', array('idGroupe' => '3', 'idPage' => '1'));
    $this->db->insert('GroupeAccedePage', array('idGroupe' => '3', 'idPage' => '2'));
    $this->db->insert('GroupeAccedePage', array('idGroupe' => '3', 'idPage' => '3'));
    $this->db->insert('GroupeAccedePage', array('idGroupe' => '3', 'idPage' => '4'));

    $select = $this->db->select('id as idPersonne, 1 as idGroupe')->from('TPersonne')->where_in('id not', array(1, 10, 14, 15, 23, 24, 27, 28, 29, 30))->get();
    if($select->num_rows()) {
        $insert = $this->db->insert_batch('PersonneAppartientGroupe', $select->result_array());
    }
    $this->db->insert('PersonneAppartientGroupe', array('idPersonne' => '1', 'idGroupe' => '2'));
    $this->db->insert('PersonneAppartientGroupe', array('idPersonne' => '1', 'idGroupe' => '3'));
    $this->db->insert('PersonneAppartientGroupe', array('idPersonne' => '10', 'idGroupe' => '3'));
    $this->db->insert('PersonneAppartientGroupe', array('idPersonne' => '14', 'idGroupe' => '3'));
    $this->db->insert('PersonneAppartientGroupe', array('idPersonne' => '15', 'idGroupe' => '3'));
    $this->db->insert('PersonneAppartientGroupe', array('idPersonne' => '23', 'idGroupe' => '3'));
    $this->db->insert('PersonneAppartientGroupe', array('idPersonne' => '24', 'idGroupe' => '3'));
    $this->db->insert('PersonneAppartientGroupe', array('idPersonne' => '27', 'idGroupe' => '3'));
    $this->db->insert('PersonneAppartientGroupe', array('idPersonne' => '28', 'idGroupe' => '3'));
    $this->db->insert('PersonneAppartientGroupe', array('idPersonne' => '29', 'idGroupe' => '3'));
    $this->db->insert('PersonneAppartientGroupe', array('idPersonne' => '30', 'idGroupe' => '3'));
  }

  public function up() {
    $this->creerTable();
    $this->initialiserDonneesTable();
  }

  public function down() {
    $this->dbforge->drop_table('PersonneAppartientGroupe', true);
    $this->dbforge->drop_table('TGroupe', true);
    $this->dbforge->drop_table('GroupeAccedePage', true);
    $this->dbforge->drop_table('TPage', true);
  }
}
?>