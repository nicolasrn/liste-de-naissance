<?php
  class Articles_model extends CI_Model {
    public function __construct() {
      $this->load->database();
    }

    public function get($id) {
      $article = $this->db->get_where('TArticle', array('id' => $id))->first_row();
      $article->img = $this->getImage($article->id);
      return $article;
    }

    public function getArticles($personne = null, $type = null, $etat = 0) {
      $data = array($etat, $personne, $type, $etat, $personne, $type);
      $sql = '
      select * 
      from (
        SELECT a.id, a.libelle, COALESCE(a.quantiteSouhaitee, 0) quantiteSouhaitee, COALESCE(sum(r.quantiteReservee), 0) as quantiteReserveeTotale
        FROM TArticle a
        left join PersonneReserveCadeau r on a.id = r.idArticle and quantiteReservee > 0
        where etat = ?
        and aDestinationDe = ?
        and type = ?
        group by a.id, libelle, quantiteSouhaitee
        having COALESCE(a.quantiteSouhaitee, 0) > COALESCE(sum(r.quantiteReservee), 0)
        order by lower(a.libelle) asc
      ) a union 
      select * 
      from (
        SELECT a.id, a.libelle, COALESCE(a.quantiteSouhaitee, 0) quantiteSouhaitee, COALESCE(sum(r.quantiteReservee), 0) as quantiteReserveeTotale
        FROM TArticle a
        left join PersonneReserveCadeau r on a.id = r.idArticle and quantiteReservee > 0
        where etat = ?
        and aDestinationDe = ?
        and type = ?
        group by a.id, libelle, quantiteSouhaitee
        having COALESCE(a.quantiteSouhaitee, 0) = COALESCE(sum(r.quantiteReservee), 0)
        order by lower(a.libelle) asc
      ) b';
      $query = $this->db->query($sql, $data);

      $res = array();
      foreach ($query->result() as $row) {
        $row->quantiteReserveeUtilisateur = $this->getQuantiteReserveeUtilisateur($row->id, $this->input->get('idUser'));
        $row->img = $this->getImage($row->id);
        $res[] = $row;
      }
      return $res;
    }

    public function getDetailReservations($idPersonne, $type) {
      $sql = 'SELECT a.libelle, r.quantiteReservee, p.login 
        FROM TArticle a 
        join PersonneReserveCadeau r on a.id = r.idArticle and r.quantiteReservee > 0 
        join TPersonne p on p.id = r.idPersonne
        where a.type = ?
        and a.aDestinationDe = ?
        order by p.login, r.quantiteReservee';
      $query = $this->db->query($sql, array($type, $idPersonne));
      return $query->result();
    }

    private function getQuantiteReserveeUtilisateur($idArticle, $idUser) {
      $query = $this->db->query('select prc.quantiteReservee from PersonneReserveCadeau prc where prc.idArticle = ? and prc.idPersonne = ?', array($idArticle, $idUser));
      foreach ($query->result() as $row) {
        return $row->quantiteReservee;
      }
      return 0;
    }

    private function getImage($id) {
      $query = $this->db->query('select id, src from TImage where idArticle = ?', $id);
      $images = array();
      foreach ($query->result() as $row) {
        $images[] = array('src' => $row->src, 'id' => $row->id);
      }
			return $images;
    }

    public function enregistrerArticle($personne, $type, $id) {
      $idArticle = $this->input->post('idArticle');
      $res = $this->handleFiles();
      if ($idArticle == -1) {
        $sql = 'insert into TArticle (libelle, quantiteSouhaitee, etat, type, aDestinationDe) values (?, ?, ?, ?, ?)';
        $query = $this->db->query($sql, array(
          $this->input->post('libelle'), 
          $this->input->post('quantiteSouhaitee'), 
          $this->input->post('etat'), 
          $type, 
          $personne));
          $idArticle = $this->db->insert_id();
      } else {
        $aSupprimer = explode(';', $this->input->post('toDelete'));
        foreach ($aSupprimer as $key => $value) {
          $this->db->delete('TImage', array('id' => $value));
        }
        $this->db->where('id', $idArticle);
        $this->db->update('TArticle', array(
          'libelle' => $this->input->post('libelle'),
          'quantiteSouhaitee' => $this->input->post('quantiteSouhaitee'), 
          'etat' => $this->input->post('etat')
        ));
      }
      $this->enregistrerImage($res['data'], $idArticle);
      return $res;
    }

    private function handleFiles() {
      $count = count($_FILES);
      $config['upload_path']          = "img";
      $config['allowed_types']        = 'gif|jpg|png';
      $config['max_size']             = 2048;
      $config['overwrite']            = true;

      $this->load->library('upload', $config);
      $res = array('messages' => array(), 'data' => array());
      for($index = 1; $index <= $count; $index++) {
        if (!$this->upload->do_upload('image-' . $index)) {
          $res[] = $this->upload->display_errors();
        } else {
          $res['data'][] = $this->upload->data();
          $res['messages'][] = "l'image " . $this->upload->data()['orig_name'] . " a bien été rajouté";
        }
      }
      return $res;
    }

    private function enregistrerImage($files, $idArticle) {
      $sql = 'insert into TImage (src, idArticle) values (?, ?)';
      foreach($files as $file) {
        $query = $this->db->query($sql, array(
          '/img/' . $file['file_name'], 
          $idArticle));
      }
    }

    public function supprimerArticle() {
      $idArticle = $this->input->post('idArticle');
      $quantiteReserveeSurArticleASupprimer = 0;
      $query = $this->db->query('select COALESCE(sum(quantiteReservee), 0) as quantiteReservee from PersonneReserveCadeau where idArticle = ?', $idArticle);
      foreach ($query->result() as $row) {
        $quantiteReserveeSurArticleASupprimer = $row->quantiteReservee;
        break;
      }
      if ($quantiteReserveeSurArticleASupprimer == 0) {
        $this->db->where('id', $idArticle);
        $this->db->update('TArticle', array('etat' => 1));
        return "suppression effectuée avec succès";
      }
      return "impossible de supprimer, des réservations ont été effectuée sur cet article";
    }

    public function restaurerArticle() {
      $idArticle = $this->input->post('idArticle');
      $this->db->where('id', $idArticle);
      $this->db->update('TArticle', array('etat' => 0));
      return "restauration effectuée avec succès";
    }
  }
?>