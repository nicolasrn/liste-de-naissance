<div class="jumbotron">
  <h1>Qui est inscrit ?</h1>
</div>

<div id="filtreHabilitationParChamp">
  <fieldset>
    <legend><h2>Filtre par champ</h2></legend>
  </fieldset>
</div>

<div>
  <table class="table table-responsive table-striped table-hover" data-plugin="dataTable">
    <thead>
      <tr>
        <th class='searchable'>id</th>
        <th class='searchable'>login</th>
        <th class='searchable'>password</th>
        <th class='searchable'>email</th>
        <th class='searchable'>réservation totale</th>
        <th class='searchable'>groupes</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th>id</th>
        <th>login</th>
        <th>password</th>
        <th>email</th>
        <th>réservation totale</th>
        <th>groupes</th>
      </tr>
    </tfoot>
    <tbody>
      {personnes}
          <tr>
            <td>{id}</td>
            <td>{login}</td>
            <td>{password}</td>
            <td>{email}</td>
            <td>{reservationTotale}</td>
            <td>{libelleGroupe}</td>
          </tr>
      {/personnes}
    </tbody>
  </table>
</div>

<script>
  lds.config.datatable.searchOnColonne = true;
  lds.config.datatable.defautFormLocation = '#filtreHabilitationParChamp fieldset';
  lds.config.datatable.locationType = 'form';
  lds.config.datatable.searchColonne = '.searchable';
  lds.config.datatable.searching = false;
</script>