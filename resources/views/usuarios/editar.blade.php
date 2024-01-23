@extends('layout')

@section('conteudo')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">Editar Usuário</h5>
                </div>
            </div>
            <form action="{{ route('usuarios.update') }}" method="post" enctype="multipart/form-data">
              <input type="hidden" name="id" value="{{ $usuario->id }}">
              @csrf
              <div class="row mt-2 gy-4">
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input required class="form-control" type="text" id="nome" name="nome" value='{{ $usuario->nome }}' placeholder="Nome"/>
                    <label for="nome">Nome:</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input required class="form-control" type="text" id="email" name="email" placeholder="john.doe@example.com" value='{{ $usuario->email }}' />
                    <label for="email">E-mail:</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <select required id="tipo" name='tipo' class="select2 form-select">
                      <option value="">Opções</option>
                      <option @if( $usuario->tipo == 'Administrador' ) selected @endif value="Administrador">Administrador</option>
                      <option @if( $usuario->tipo == 'Contador' ) selected @endif value="Contador">Contador</option>
                    </select>
                    <label for="tipo">Tipo de Usuário:</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <select required id="genero" name='genero' class="select2 form-select">
                      <option value="">Opções</option>
                      <option @if( $usuario->genero == 'Masculino' ) selected @endif value="Masculino">Masculino</option>
                      <option @if( $usuario->genero == 'Feminino' ) selected @endif value="Feminino">Feminino</option>
                    </select>
                    <label for="genero">Gênero:</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input class="form-control" type="file" id="imagem" name="imagem"/>
                    <label for="imagem">Imagem:</label>
                  </div>
                </div>
              </div>
              <div class="mt-4">
                  <button type="submit" class="btn btn-primary me-2">Salvar</button>
              </div>
            </form>
        </div>
    </div>
</div>
@endsection
