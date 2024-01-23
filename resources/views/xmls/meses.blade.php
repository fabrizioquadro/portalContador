@extends('layout')

@section('conteudo')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h5 class="card-title">Cliente: {{ $cliente->nome }} - Meses </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @foreach($distincts as $linha)
                        <a href="{{ route('xml.listar',[$cliente->id,$linha->mes_ano]) }}" class="btn btn-primary">{{ str_replace('_','/',$linha->mes_ano) }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
