@extends('layout')

@section('conteudo')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="tabela-index display" id="table-index">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nome</th>
                                    <th>Cnpj</th>
                                    <th>Email</th>
                                    <th>Tel</th>
                                    <th>Cel</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @foreach($clientes as $cliente)
                              <tr>
                                    <td>{{ $cliente->id }}</td>
                                    <td>{{ $cliente->nome }}</td>
                                    <td>{{ cpfCnpjDbForm($cliente->cnpj) }}</td>
                                    <td>{{ $cliente->email }}</td>
                                    <td>{{ $cliente->tel }}</td>
                                    <td>{{ $cliente->cel }}</td>
                                    <td>
                                        <a href="{{ route('backupsListar', $cliente->id) }}" class="btn btn-primary btn-sm">Backups</a>
                                    </td>
                              </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
window.addEventListener('load',()=>{
  $('#table-index').DataTable({
    order: [[0, 'asc']],
    "language": {
			"sEmptyTable": "Nenhum registro encontrado",
      "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
      "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
      "sInfoFiltered": "(Filtrados de _MAX_ registros)",
      "sInfoPostFix": "",
      "sInfoThousands": ".",
      "sLengthMenu": "_MENU_ resultados por página",
      "sLoadingRecords": "Carregando...",
      "sProcessing": "Processando...",
      "sZeroRecords": "Nenhum registro encontrado",
      "sSearch": "Pesquisar",
      "oPaginate": {
        "sNext": "Próximo",
        "sPrevious": "Anterior",
        "sFirst": "Primeiro",
        "sLast": "Último"
      },
      "oAria": {
        "sSortAscending": ": Ordenar colunas de forma ascendente",
        "sSortDescending": ": Ordenar colunas de forma descendente"
      }
    }
  });
})

</script>

@endsection
