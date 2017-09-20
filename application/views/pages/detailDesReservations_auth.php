<div class="jumbotron">
  <h1>Qui a réservé quoi ?</h1>
</div>

<table class="table table-responsive table-striped table-hover" data-plugin="dataTable">
  <thead>
    <tr>
      <th>login</th>
      <th>article</th>
      <th>quantité réservée</th>
    </tr>
  </thead>
  <tbody>
    {reservations}
      <tr>
        <td>{login}</td>
        <td>{libelle}</td>
        <td>{quantiteReservee}</td>
      </tr>
    {/reservations}
  </tbody>
</table>