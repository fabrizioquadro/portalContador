@extends('layout')

@section('conteudo')
@php
if($usuario->imagem == ""){
    if($usuario->genero == "Masculino"){
        $avatar = "/public/materialize/img/avatars/1.png";
    }
    else{
        $avatar = "/public/materialize/img/avatars/2.png";
    }
}
else{
    $avatar = "/public/img/usuarios/".$usuario->imagem;
}
@endphp
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-body">
            <div class="d-flex align-items-start align-items-sm-center gap-4">
              <img
                src="{{ asset($avatar) }}"
                alt="user-avatar"
                class="d-block w-px-120 h-px-120 rounded"
                id="uploadedAvatar" />
              <div class="button-wrapper">
                <h4 class="card-header">Visualizar Usuário</h4>
              </div>
            </div>
          </div>
          <div class="card-body pt-2 mt-1">
              <div class="row mt-2 gy-4">
                <div class="col-md-6">
                  <span class="fw-medium">Nome:</span><br>
                  <span>{{ $usuario->nome }}</span>
                </div>
                <div class="col-md-6">
                  <span class="fw-medium">Email:</span><br>
                  <span>{{ $usuario->email }}</span>
                </div>
                <div class="col-md-6">
                  <span class="fw-medium">Tipo de Usuário:</span><br>
                  <span>{{ $usuario->tipo }}</span>
                </div>
                <div class="col-md-6">
                  <span class="fw-medium">Gênero:</span><br>
                  <span>{{ $usuario->genero }}</span>
                </div>
              </div>
          </div>
          <!-- /Account -->
        </div>
      </div>
    </div>
</div>
@endsection
