<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_v1_TArticle extends CI_Migration {
  
  public function initialiserDonneesTable() {
    $fields = array(
      'ordrePrix' => array('type' => 'decimal(11, 2)'),
      'lieu' => array('type' => 'varchar(100)'),
      'url' => array('type' => 'varchar(100)'),
      'libelleUrl' => array('type' => 'varchar(100)')
    );
    $this->dbforge->add_column('TArticle', $fields);
  }

  public function up() {
    $this->initialiserDonneesTable();
  }

  public function down() {
    $this->dbforge->drop_column('TArticle', 'ordrePrix');
    $this->dbforge->drop_column('TArticle', 'lieu');
    $this->dbforge->drop_column('TArticle', 'url');
    $this->dbforge->drop_column('TArticle', 'libelleUrl');
  }
}
?>