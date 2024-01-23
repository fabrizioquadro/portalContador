@extends('layout')

@section('conteudo')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Dashboard</h5>
            <div class="row">
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-primary"><i class="mdi mdi-account-supervisor mdi-20px"></i></span>
                                </div>
                                <h4 class="ms-1 mb-0 display-6">{{ $qtClientes }}</h4>
                            </div>
                            <p class="mb-0 text-heading">Numero de clientes</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Lista de Clientes</h6>
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
                                    <th>Ultímo Envio</th>
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
                                    <td>{{ buscaUltimoImportCliente($cliente->id) }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow show" data-bs-toggle="dropdown" aria-expanded="true">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(-101.111px, 134.444px);">
                                                <a class="dropdown-item waves-effect" href="{{ route('xml') }}?id_cliente={{ $cliente->id }}"> Relação de Xml</a>
                                                <a class="dropdown-item waves-effect" href="{{ route('vendas') }}?id_cliente={{ $cliente->id }}"> Relatório de Vendas</a>
                                                <a class="dropdown-item waves-effect" href="{{ route('inventariosListar', $cliente->id) }}"></i> Inventários</a>
                                            </div>
                                        </div>
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
