
<div class="jumbotron">
  <div class="container">
    <h1>Bonjour</h1>
    <p>
      Pour aller plus loin merci de vous authentifier ou de vous inscrire ;-)
    </p>
  </div>
</div>

<div class="col-md-offset-3 col-md-6">
  <div data-show-message="<?php echo $this->session->flashdata('enregistrementOk') !== null && $this->session->flashdata('enregistrementOk'); ?>" data-message="Félicitation vous vous êtes enregistré, vous pouvez vous connecter" data-niveau="text-info"></div> 
  <form method="POST" action="<?php echo site_url('/home/connexion'); ?>" id="form-authentification" class="form-horizontal">
    <?php echo validation_errors(); ?>
    <div class="<?php if (isset($erreur)) echo 'text-danger'; ?>"><?php if (isset($erreur)) echo $erreur ?></div>
    
    <div class="form-group">
      <label for="login" class="col-md-3 control-label">Login</label>
      <div class="col-md-9">
        <input type="text" placeholder="login" id="login" name="login" class="form-control">
      </div>
    </div>
    <div class="form-group">
      <label for="mdp" class="col-md-3 control-label">Mot de passe</label>
      <div class="col-md-9">
        <input type="password" placeholder="Mot de passe" id="mdp" name="password" class="form-control">
      </div>
    </div>
    <div class="col-md-offset-3 col-md-9">
      <button type="submit" class="btn btn-success">Se connecter</button>
      <a class="btn btn-warning" href="<?php echo site_url('/home/inscription'); ?>" role="button">S'enregistrer</a>
    </div>
  </form>
</div>