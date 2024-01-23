@extends('login.layout')

@section('conteudo')
<div class="card-body mt-2">
  <h4 class="mb-2">Recuperar Senha</h4>
  <p class="mb-4">Você receberá uma nova senha no seu email.</p>
  @if($mensagem = Session::get('mensagem'))
      <div class="alert alert-solid-danger" role="alert">
          {{ $mensagem }}
      </div>
  @endif
  <form id="formAuthentication" class="mb-3" action="{{ route('recuperarSenha') }}" method="POST">
    @csrf
    <div class="form-floating form-floating-outline mb-3">
      <input type="email" class="form-control" id="email" name="email" placeholder="Email" autofocus />
      <label for="email">Email</label>
    </div>
    <div class="mb-3">
        <button class="btn btn-primary d-grid w-100" type="submit">Recuperar Senha</button>
    </div>
  </form>
</div>
@endsection
