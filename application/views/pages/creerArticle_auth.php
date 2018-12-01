<?php 
if (isset($menuAdministrationListeDeSouhaits)) {
  echo $menuAdministrationListeDeSouhaits;
}
?>

<div class="jumbotron">
  <h1>Ajout / Modification d'article</h1>
</div>
<form class="form-horizontal" action="<?php echo $urlEnregistrement; ?>" method="POST" enctype="multipart/form-data" id="ajoutArticle">
  <div class="form-group" id="resultatAjoutArticle">
    <span class="help-block">
      <?php 
        if (isset($messages) && !empty($messages)) {
          echo "<ul class='bg-success'>";
          foreach($messages as $message) {
            echo "<li>$message</li>";
          }
          echo "</ul>";
        }
      ?>
    </span>
  </div>
  <div class="form-group">
  <?php echo validation_errors(); ?>
  </div>
  <div class="form-group">
    <label for="libelleArticle" class="col-sm-2 control-label">Libellé de l'article</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="libelle" value="<?php echo $libelle; ?>" id="libelleArticle" placeholder="Libellé de l'article">
    </div>
  </div>
  <div class="form-group">
    <label for="ordrePrix" class="col-sm-2 control-label">ordre de prix</label>
    <div class="col-sm-10">
      <input type="number" step="0.01" class="form-control" name="ordrePrix" value="<?php echo $ordrePrix; ?>" id="ordrePrix" placeholder="ordre de prix">
    </div>
  </div>
  <div class="form-group">
    <label for="lieu" class="col-sm-2 control-label">lieu</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="lieu" value="<?php echo $lieu; ?>" id="lieu" placeholder="lieu">
    </div>
  </div>
  <div class="form-group">
    <label for="libelleUrl" class="col-sm-2 control-label">libellé et url</label>
    <div class="col-sm-10">
      <div class="form-inline">
        <label for="libelleUrl" class="sr-only">libellé</label>
        <input type="text" class="form-control" name="libelleUrl" value="<?php echo $libelleUrl; ?>" id="libelleUrl" placeholder="libelle du lien">
        <label for="url" class="sr-only">url</label>
        <input type="url" class="form-control" name="url" value="<?php echo $url; ?>" id="url" placeholder="url">
      </div>
    </div>
  </div>
  <div class="form-group">
    <label for="etat" class="col-sm-2 control-label">Etat</label>
    <div class="col-sm-10">
      <select name="etat" class="form-control">
        <?php echo $optionsEtat ?>
      </select> 
    </div>
  </div>
  <div class="form-group compteur" id="compteur-nouvelArticle">
    <label for="quantiteSouhaitee" class="col-sm-2 control-label">Quantite Souhaitée</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" name="quantiteSouhaitee" value="<?php echo $quantiteSouhaitee; ?>" id="quantiteSouhaitee" value="0" readonly/>
    </div>
    <div class="input-group-btn col-sm-12 col-md-12">
      <button type="button" class="btn btn-default moins">-</button>
      <button type="button" class="btn btn-default plus">+</button>
    </div>
  </div>
  <div class="list-image">
    <?php
      if (isset($images) && !empty($images)) {
        foreach($images as $ligneImage) {
          echo '<div class="row">';
          foreach($ligneImage as $img) {
            echo '<div class="col-md-3" data-id="' . $img['id'] . '"><a href="#" class="thumbnail glyphicon glyphicon-remove">' . '<img src="' . base_url() . '/' . $img['src'] . '">' . '</a></div>';
          }
          echo '</div>';
        }
      }
    ?>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="button" id="ajouterImage" class="btn btn-primary"><span class="glyphicon glyphicon-file"></span> Ajouter une image</button>
      <button type="reset" class="btn btn-warning"><span class="glyphicon glyphicon-refresh"></span> Vider</button>
      <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-floppy-disk"></span> Enregistrer l'article</button>
    </div>
  </div>
  <input type="hidden" name="action" value="creerOuMaj"/>
  <input type="hidden" name="idArticle" value="<?php if (isset($id)) echo $id; else echo '-1'; ?>"/>
  <input type="hidden" name="idPersonne" value="<?php if (isset($personne)) echo $personne; else echo '-1'; ?>"/>
  <input type="hidden" name="toDelete" value=""/>
  <input type="hidden" name="nbImage" id="nbImage" value="0">
</form>
<form class="form-horizontal" action="<?php echo $urlEnregistrement; ?>" method="POST" id="supprimerArticle">
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span> Supprimer l'article</button>
    </div>
  </div>
  <input type="hidden" name="action" value="supprimer"/>
  <input type="hidden" name="idArticle" value="<?php if (isset($id)) echo $id; else echo '-1'; ?>"/>
</form>
<form class="form-horizontal" action="<?php echo $urlEnregistrement; ?>" method="POST" id="restaurerArticle">
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-repeat"></span> Restaurer l'article</button>
    </div>
  </div>
  <input type="hidden" name="action" value="restaurer"/>
  <input type="hidden" name="idArticle" value="<?php if (isset($id)) echo $id; else echo '-1'; ?>"/>
</form>