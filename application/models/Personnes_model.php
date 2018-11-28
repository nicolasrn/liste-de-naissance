<?php
  class Personnes_model extends CI_Model {
    public function __construct() {
      $this->load->database();
    }

    public function get() {
      $query = $this->db->select('p.id, p.login, p.password, p.email, (select sum(prc.quantiteReservee) from PersonneReserveCadeau prc where prc.idPersonne = p.id) as reservationTotale, GROUP_CONCAT(g.libelleGroupe SEPARATOR \';\') as libelleGroupe')
                        ->from('TPersonne p')
                        ->join('PersonneAppartientGroupe pg', 'pg.idPersonne = p.id', 'left')
                        ->join('TGroupe g', 'g.id = pg.idGroupe', 'left')
                        ->where('isAdmin', 0)
                        ->group_by(array('p.id', 'p.login', 'p.password', 'p.email'));
      return $query->get()->result();
    }

    public function getAllowsPages($idPersonne) {
      $query = $this->db->select('p.id, p.login, g.libelleGroupe, pa.url')
                        ->from('TPersonne p')
                        ->join('PersonneAppartientGroupe pg', 'pg.idPersonne = p.id')
                        ->join('TGroupe g', 'g.id = pg.idGroupe')
                        ->join('GroupeAccedePage gp', 'gp.idGroupe = g.id')
                        ->join('TPage pa', 'pa.id = gp.idPage');
      if ($idPersonne != null) {
        $query->where('p.id', $idPersonne);
      }
      return $query->get()->result();
    }

    public function getGroupesAutorises($idPersonne = null) {
      $query = $this->db->select('p.id, p.login, GROUP_CONCAT(g.id SEPARATOR \';\') as idGroupe, GROUP_CONCAT(g.libelleGroupe SEPARATOR \';\') as libelleGroupe')
                        ->from('TPersonne p')
                        ->join('PersonneAppartientGroupe pg', 'pg.idPersonne = p.id', 'left')
                        ->join('TGroupe g', 'g.id = pg.idGroupe', 'left')
                        ->group_by(array('p.id', 'p.login'));
      if ($idPersonne != null) {
        $query->where('p.id', $idPersonne);
      }
      return $query->get()->result();
    }

    public function getGroupes() {
      return $this->db->get('TGroupe')->result();
    }

    public function updateHabilitations() {      
      $idPersonne = $this->input->post('idPersonne');
      $hab = $this->input->post('inputHabilitation[]');

      $data = array();

      $this->db->delete('PersonneAppartientGroupe', array('idPersonne' => $idPersonne));
      foreach($hab as $key => $val) {
        $this->db->insert('PersonneAppartientGroupe', array('idPersonne' => $idPersonne, 'idGroupe' => $val));
      }
    }

    public function getReservations() {
      return $this->db->select('p.login, p.email, a.libelle, a.type, a.aDestinationDe, prc.quantiteReservee')
                  ->from('TPersonne p')
                  ->join('PersonneReserveCadeau prc', 'prc.idPersonne = p.id')
                  ->join('TArticle a', 'prc.idArticle = a.id')
                  ->order_by('a.type', 'desc')
                  ->order_by('p.login')
                  ->order_by('prc.quantiteReservee')
                  ->get()->result();
    }

    public function getAllUtilisateurs() {
      return $this->db->select('id, login')->get("TPersonne")->result();
    }

  }
?>