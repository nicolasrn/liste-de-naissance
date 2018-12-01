<div class="jumbotron">
  <h1>Habilitations</h1>
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
        <th class='searchable'>groupes</th>
        <th>modification des groupes</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th>id</th>
        <th>login</th>
        <th>groupes</th>
        <th>modification des groupes</th>
      </tr>
    </tfoot>
    <tbody>
      {personnes}
        <tr>
          <td>{id}</td>
          <td>{login}</td>
          <td>{libelleGroupe}</td>
          <td class="csvToCheckbox">{idGroupe}</td>
        </tr>
      {/personnes}
    </tbody>
  </table>
  <form method="POST" action="<?php echo site_url('/personnes/update/habilitation'); ?>" class="form-inline" id="template" data-id="{groupes}{id};{/groupes}" data-libelle="{groupes}{libelleGroupe};{/groupes}">
    <div class="checkbox">
      <label for=""/>
      <input class="form-control" type="checkbox" value=""/>
    </div>
    <input type="hidden" name="idPersonne"/>
  </form>
</div>

<script>
  lds.config.datatable.searchOnColonne = true;
  lds.config.datatable.defautFormLocation = '#filtreHabilitationParChamp fieldset';
  lds.config.datatable.locationType = 'form';
  lds.config.datatable.searchColonne = '.searchable';
  lds.config.datatable.searching = false;
  addEventListener('DOMContentLoaded', function(event) {
    lds.toCheckBox('#template', '.csvToCheckbox');
  }, false);
</script>