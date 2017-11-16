<div class="jumbotron">
<h1>Qui est inscrit ?</h1>
</div>
<div>
<table class="table table-responsive table-striped table-hover" data-plugin="dataTable">
  <thead>
    <tr>
      <th>id</th>
      <th>login</th>
      <th>password</th>
      <th>email</th>
      <th>réservation totale</th>
      <th>groupes</th>
    </tr>
  </thead>
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

<script src="/assets/js/lds.js"></script>