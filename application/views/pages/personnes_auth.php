<div class="jumbotron">
  <h1>Qui est inscrit ?</h1>
</div>
<div>
  <table class="table table-responsive table-striped table-hover">
    <thead>
      <tr>
        <th>login</th>
        <th>mot de passe</th>
        <th>accès espace famille</th>
      </tr>
    </thead>
    <tbody>
      {personnes}
          <tr>
            <td>{login}</td>
            <td>{password}</td>
            <td>{isMembreFamille}</td>
          </tr>
      {/personnes}
    </tbody>
  </table>
</div>