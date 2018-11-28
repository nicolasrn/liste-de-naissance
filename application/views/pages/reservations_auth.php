<div class="jumbotron">
  <h1>Qui reserve quoi ?</h1>
</div>

<div id="filtreHabilitationParChamp">
  <fieldset>
    <legend><h2>Filtre par champ</h2></legend>
    <div class="elt-form form-group">
      <label for="pourType" class="col-sm-2 control-label">type de souhait</label>
      <div class="col-sm-10">
        <select class="form-control" name="type" id="pourType">
          <option value=""></option>
        {types}
          <option value="{type}-{annee}">{libellePour} {libelleType}-{annee}</option>
        {/types}
        </select>
      </div>
    </div>
  </fieldset>
</div>

<div>
  <table class="table table-responsive table-striped table-hover" data-plugin="dataTable">
    <thead>
      <tr>
        <th class='searchable'>login</th>
        <th class=''>email</th>
        <th class='searchable'>libelle</th>
        <th class=''>type</th>
        <th class=''>aDestinationDe</th>
        <th class=''>quantiteReservee</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th>login</th>
        <th>email</th>
        <th>libelle</th>
        <th>type</th>
        <th>aDestinationDe</th>
        <th>quantiteReservee</th>
      </tr>
    </tfoot>
    <tbody>
      {reservations}
          <tr>
            <td>{login}</td>
            <td>{email}</td>
            <td>{libelle}</td>
            <td>{type}</td>
            <td>{aDestinationDe}</td>
            <td>{quantiteReservee}</td>
          </tr>
      {/reservations}
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