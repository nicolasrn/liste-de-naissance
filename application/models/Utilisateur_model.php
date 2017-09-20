<?php
  class Utilisateur_model extends CI_Model {
    public function __construct() {
      $this->load->database();
    }

    public function get_utilisateur($login, $password) {
      $query = $this->db->query('select id, login from TPersonne where login = ? and password = ?', array($login, $password));

      foreach ($query->result_array() as $row) {
        return array(
          'id' => $row['id'],
          'login' => $row['login']
        );
      }
      return null;
    }

    public function isAdmin() {
      $utilisateur = json_decode(get_cookie('utilisateur'));
      if (!$utilisateur) {
        return false;
      }
      $query = $this->db->select('isAdmin')
                        ->where('id', $utilisateur->id)
                        ->from('TPersonne')
                        ->get();
      foreach($query->result() as $row) {
        return $row->isAdmin == 1;
      }
      return false;
    }

    /**
     * on ne donne accès qu'au cercle familliale :
     * Daniel, Jaqueline, Julie, Lilianne, Stéphane, Nicolas, Laurence
     */
    public function isMembreFamille() {
      $utilisateur = json_decode(get_cookie('utilisateur'));
      if (!$utilisateur) {
        return false;
      }
      $query = $this->db->select('isMembreFamille')
                        ->where('id', $utilisateur->id)
                        ->from('TPersonne')
                        ->get();
      foreach($query->result() as $row) {
        return $row->isMembreFamille == 1;
      }
      return false;
    }

    public function enregistrerUtilisateur() {
      $data = array(
        'login' => $this->input->post('login'),
        'password' => $this->input->post('password'),
        'isAdmin' => 0
      );
      $this->db->set($data);
      $this->db->insert('TPersonne');
    }

    public function verificationLoginUnique($login) {
      $this->db->from('TPersonne');
      $this->db->where('login', $login);
      return $this->db->count_all_results() == 0;
    }

    public function verificationEmailUnique($email) {
      $this->db->from('TPersonne');
      $this->db->where('email', $email);
      return $this->db->count_all_results() == 0;
    }

    public function getPageParDefaut() {
      
    }
  }
?>