<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_v1 extends CI_Migration {
  private function ajouterTypesSurArticle() {
    $field = array(
      'type' => array('type' => 'varchar(255)'),
      'aDestinationDe' => array('type' => 'varchar(255)') 
    );
    $this->dbforge->add_column('TArticle', $field);

    $this->db->set('type', 'naissance');
    $this->db->set('aDestinationDe', 'chloe');
    $this->db->update('TArticle');
  }

  public function ajouterIsMembreFamilleSurTPersonne() {
    $field = array('isMembreFamille' => array('type' => 'int'));
    $this->dbforge->add_column('TPersonne', $field);

    $this->db->set('isMembreFamille', '0');
    $this->db->update('TPersonne');

    $this->db->set('isMembreFamille', '1');
    $this->db->where_in('id', array(1, 2, 3, 10, 14, 15, 23, 24, 27, 28, 29, 30, ));
    $this->db->update('TPersonne');
  }

  public function ajouterEmailSurTPersonne() {
    $field = array('email' => array('type' => 'varchar(255)'));
    $this->dbforge->add_column('TPersonne', $field);
  }

  private function ajouterTablePage() {
    //ajout une table avec colonne path et text
    //path indique sur quel endroit de quel page le text doit s'afficher
    $this->dbforge->add_field(array(
      'path' => array(
        'type' => 'varchar(255)'
      ),
      'text' => array(
        'type' => 'text'
      )
    ));
    $this->dbforge->add_key('path', TRUE);
    $this->dbforge->create_table('TContenuPage');

    $sql = 'insert into TContenuPage (path, text) values (?, ?)';
    $query = $this->db->query($sql, array(
      "#chloe.naissance", 
      "<h1>Pour bébé</h1>
      <div class=\"leight\">
        <p>
          Vous nous demandez souvent ce qui nous ferait plaisir ou ce dont nous aurions besoin ... avec ce site, vous êtes sûrs de transformer l’essai ;-)
        </p>
        <p>
          Nous sommes déjà bien équipés en poussette trio, transat, parc, baby cook, écharpe de portage, lit à barreaux, linge premier mois, ... (évitons les surplus et les doublons)
        </p>
        <p>
          En plus de la liste, que vous trouverez ci-après, nous aimerions beaucoup pouvoir réaliser une séance photo avec bébé afin de garder de beaux souvenirs de ses premiers jours.
          Si vous le souhaitez, vous pouvez participer financièrement à cette séance photo ainsi qu'à l'achat de sièges auto et autres dépenses via, par exemple, une carte cadeau multi-enseigne.
        </p>
        <p>
          Pensez à bien indiquer les éléments que vous voulez nous offrir afin d’éviter les surplus et les doublons.
        </p>
        <p>
          Si vous avez des questions, n'hésitez pas à nous contacter ;-)					
        </p>
        <p>
          Merci d’avance pour vos délicates attentions. <br>
          Laurence, Nicolas et Mini-nous
        </p>
      </div>"));
      $this->db->insert_id();
  }

  public function up() {
    $this->ajouterTypesSurArticle();
    $this->ajouterIsMembreFamilleSurTPersonne();
    $this->ajouterEmailSurTPersonne();
    $this->ajouterTablePage();
  }

  public function down() {
    $this->dbforge->drop_column('TArticle', 'type');
    $this->dbforge->drop_column('TArticle', 'aDestinationDe');
    $this->dbforge->drop_column('TPersonne', 'isMembreFamille');
    $this->dbforge->drop_column('TPersonne', 'email');
    $this->dbforge->drop_table('TContenuPage', true);
  }
}
?>