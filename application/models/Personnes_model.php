<?php
  class Personnes_model extends CI_Model {
    public function __construct() {
      $this->load->database();
    }

    public function get() {
      $query = $this->db->select('p.id, p.login, p.password, GROUP_CONCAT(g.libelleGroupe SEPARATOR \';\') as libelleGroupe')
                        ->from('TPersonne p')
                        ->join('PersonneAppartientGroupe pg', 'pg.idPersonne = p.id', 'left')
                        ->join('TGroupe g', 'g.id = pg.idGroupe', 'left')
                        ->where('isAdmin', 0)
                        ->group_by(array('p.id', 'p.login', 'p.password'));
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
                        ->join('PersonneAppartientGroupe pg', 'pg.idPersonne = p.id')
                        ->join('TGroupe g', 'g.id = pg.idGroupe')
                        ->group_by(array('p.id', 'p.login'));
      if ($idPersonne != null) {
        $query->where('p.id', $idPersonne);
      }
      return $query->get()->result();
    }

    public function getGroupes() {
      return $this->db->get('TGroupe')->result();
    }

    /*
select p.login, g.libelleGroupe, pa.url from tpersonne p
join personneappartientgroupe pg on pg.idPersonne = p.id
join tgroupe g on g.id = pg.idGroupe
join GroupeAccedePage gp on gp.idGroupe = g.id
join tpage pa on pa.id = gp.idPage
    */
  }
?>