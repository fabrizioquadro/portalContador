@extends('layout')

@section('conteudo')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h5 class="card-title">Cliente: {{ $cliente->nome }}</h5>
                </div>
            </div>
            @if($mensagem = Session::get('mensagem'))
              <div class="alert alert-success alert-dismissible" role="alert">
                {{ $mensagem }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif
            <form action="{{ route('clientes.contadores.salvar') }}" method="post">
                <input type="hidden" name="id_cliente" value="{{ $cliente->id }}">
                @csrf
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
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
