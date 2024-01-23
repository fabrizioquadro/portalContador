@extends('layout')

@section('conteudo')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">Adicionar Usuário</h5>
                </div>
            </div>
            <form action="{{ route('usuarios.insert') }}" method="post" enctype="multipart/form-data">
              @csrf
              <div class="row mt-2 gy-4">
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input required class="form-control" type="text" id="nome" name="nome" placeholder="Nome"/>
                    <label for="nome">Nome:</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input required class="form-control" type="text" id="email" name="email" placeholder="john.doe@example.com" />
                    <label for="email">E-mail:</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-floating form-floating-outline">
                    <select required id="tipo" name='tipo' class="select2 form-select">
                      <option value="">Opções</option>
                      <option value="Administrador">Administrador</option>
                      <option value="Contador">Contador</option>
                    </select>
                    <label for="tipo">Tipo de Usuário:</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-floating form-floating-outline">
                    <select required id="genero" name='genero' class="select2 form-select">
                      <option value="">Opções</option>
                      <option value="Masculino">Masculino</option>
                      <option value="Feminino">Feminino</option>
                    </select>
                    <label for="genero">Gênero:</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-floating form-floating-outline">
                    <input required class="form-control" type="password" id="password" name="password" placeholder="********" />
                    <label for="password">Senha:</label>
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
