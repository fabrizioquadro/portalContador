@extends('layout')

@section('conteudo')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="card-title">Usuários</h5>
                </div>
                <div class="col-md-6" align='right'>
                    <a href="{{ route('usuarios.adicionar')}}" class="dt-button add-new btn btn-primary" type="button">
                      <span>
                        <i class="mdi mdi-plus me-0 me-sm-1"></i>
                        <span class="d-none d-sm-inline-block">Add Usuário</span>
                      </span>
                    </a>
                </div>
            </div>
            @if($mensagem = Session::get('mensagem'))
              <div class="alert alert-success alert-dismissible" role="alert">
                {{ $mensagem }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="tabela-index display" id="table-index">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Tipo</th>
                                    <th>Gênero</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @foreach($usuarios as $usuario)
                              @php
                              if($usuario->imagem == ""){
                                  if($usuario->genero == "Masculino"){
                                    $avatar = '/public/assets/img/avatars/1.png';
                                  }
                                  else{
                                    $avatar = '/public/assets/img/avatars/2.png';
                                  }

                              }
                              else{
                                  $avatar = '/public/img/usuarios/'.$usuario->imagem."?".date('Ymdhis');
                              }
                              @endphp
                              <tr>
                                  <td> <img src="{{ $avatar }}" style='height:40px; border-radius: 20px' alt=""> </td>
                                  <td>{{ $usuario->nome }}</td>
                                  <td>{{ $usuario->email }}</td>
                                  <td>{{ $usuario->tipo }}</td>
                                  <td>{{ $usuario->genero }}</td>
                                  <td>
                                    <div class="dropdown">
                                      <button type="button" class="btn p-0 dropdown-toggle hide-arrow show" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i class="mdi mdi-dots-vertical"></i>
                                      </button>
                                      <div class="dropdown-menu" data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(-101.111px, 134.444px);">
                                        <a class="dropdown-item waves-effect" href="{{ route('usuarios.editar', $usuario->id) }}"><i class="mdi mdi-pencil-outline me-1"></i> Editar</a>
                                        <a class="dropdown-item waves-effect" href="{{ route('usuarios.excluir', $usuario->id) }}"><i class="mdi mdi-trash-can-outline me-1"></i> Excluir</a>
                                        <a class="dropdown-item waves-effect" href="{{ route('usuarios.visualizar', $usuario->id) }}"><i class="mdi mdi-eye-outline me-1"></i> Visualizar</a>
                                        <a class="dropdown-item waves-effect" href="{{ route('usuarios.alterarSenha', $usuario->id) }}"><i class="mdi mdi-cog-outline me-1"></i> Alterar Senha</a>
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
    order: [[1, 'asc']],
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
