<div class="jumbotron">
  <h1>Formulaire d'inscription</h1>
</div>

<form class="form-horizontal" method="POST" action="<?php echo site_url('/home/enregistrement'); ?>" id="enregistrement">
  <?php echo validation_errors(); ?>
  <div class="form-group">
    <label for="login" class="col-sm-4 control-label">Login</label>
    <div class="col-sm-8">
      <input type="text" name="login" class="form-control" id="login" placeholder="login" value="<?php echo set_value('login'); ?>">
      <span class="help-block"></span>
    </div>  
  </div>
  <div class="form-group">
    <label for="email" class="col-sm-4 control-label">Email</label>
    <div class="col-sm-8">
      <input type="email" name="email" class="form-control" id="email" placeholder="adresse@mail.fr" value="<?php echo set_value('email'); ?>">
      <span class="help-block"></span>
    </div>
  </div>
  <div class="form-group">
    <label for="password" class="col-sm-4 control-label">Mot de passe</label>
    <div class="col-sm-8">
      <input type="password" name="password" class="form-control" id="password" placeholder="mot de passe">
      <span class="help-block"></span>
    </div>
  </div>
  <div class="form-group">
    <label for="password2" class="col-sm-4 control-label">Confirmer le mot de passe</label>
    <div class="col-sm-8">
      <input type="password" name="password2" class="form-control" id="password2" placeholder="confirmer le mot de passe">
      <span class="help-block"></span>
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
      <button type="submit" class="btn btn-success">S'enregistrer</button>
    </div>
  </div>
</form>