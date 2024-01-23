@extends('layout')

@section('conteudo')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">Editar Cliente</h5>
                </div>
            </div>
            <form action="{{ route('clientes.update') }}" method="post">
                <input type="hidden" name="id" value="{{ $cliente->id }}">
              @csrf
              <div class="row mt-2 gy-4">
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input required class="form-control" type="text" id="nome" name="nome" placeholder="Razão Social" value="{{ $cliente->nome }}"/>
                    <label for="nome">Razão Social:</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input required class="form-control" type="text" id="fantasia" name="fantasia" placeholder="Nome Fantasia" value="{{ $cliente->fantasia }}"/>
                    <label for="fantasia">Nome Fantasia:</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input required class="form-control" type="text" id="email" name="email" placeholder="john.doe@example.com" value="{{ $cliente->email }}" />
                    <label for="email">E-mail:</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input required class="form-control" type="text" id="cnpj" name="cnpj" placeholder="Cnpj" maxlength="18" onkeypress="formatar('##.###.###/####-##', this)" value="{{ cpfCnpjDbForm($cliente->cnpj) }}" />
                    <label for="cnpj">Cnpj:</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input class="form-control" type="text" id="tel" name="tel" placeholder="Telefone" maxlength="15" onkeypress="mascara( this, mtel )" value="{{ $cliente->tel }}" />
                    <label for="tel">Telefone:</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input required class="form-control" type="text" id="cel" name="cel" placeholder="Celular" maxlength="15" onkeypress="mascara( this, mtel )" value="{{ $cliente->cel }}" />
                    <label for="cel">Celular:</label>
                  </div>
                </div>
              </div>
              <div class="card mt-3">
                  <div class="card-body">
                      <h6 class="card-title">Contadores</h6>
                      <table class="table">
                          <thead>
                              <tr>
                                  <th style='width: 15%'>Acesso</th>
                                  <th>Contador</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach($usuarios as $usuario)
                                  <tr>
                                      <td> <input type="checkbox" {{ verificaClienteContador($cliente->id, $usuario->id) }} name="user{{ $usuario->id }}" value="Sim"> </td>
                                      <td>{{ $usuario->nome }}</td>
                                  </tr>
                              @endforeach
                          </tbody>
                      </table>
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
