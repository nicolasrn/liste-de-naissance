<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_v1_database extends CI_Migration {
  public function up() {
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'varchar(128)'
      ),
      'ip_address' => array(
        'type' => 'varchar(45)'
      ),
      'timestamp' => array(
        'type' => 'int(45)'
      ),
      'data' => array(
        'type' => 'blob'
      )
    ));
    $this->dbforge->add_key('timestamp', TRUE);
    $this->dbforge->create_table('ci_sessions');
  }

  public function down() {
    $this->dbforge->drop_table('ci_sessions', true);
  }
}
?>