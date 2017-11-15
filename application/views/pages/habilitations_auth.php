<div class="jumbotron">
  <h1>Qui est inscrit ?</h1>
</div>

<div>
  <table class="table table-responsive table-striped table-hover" data-plugin="dataTable">
    <thead>
      <tr>
        <th>id</th>
        <th>login</th>
        <th>groupes</th>
      </tr>
    </thead>
    <tbody>
      {personnes}
        <tr>
          <td>{id}</td>
          <td>{login}</td>
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

<script src="/assets/js/lds.js"></script>
<script>
lds.toCheckBox('#template', '.csvToCheckbox');
</script>