@extends('layout')

@section('conteudo')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h5 class="card-title">Relação de Xml's - Filtrar</h5>
                </div>
            </div>
            @if($mensagem = Session::get('mensagem'))
              <div class="alert alert-success alert-dismissible" role="alert">
                {{ $mensagem }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif
            <form  action="{{ route('xmlFiltrar') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <div class="form-floating form-floating-outline">
                            <select required id="id_cliente" name='id_cliente' class="select2 form-select">
                                <option></option>
                                @foreach($clientes as $cliente)
                                    <option @php if(@$_GET['id_cliente'] == $cliente->id) { echo "selected"; } @endphp value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                @endforeach
                            </select>
                            <label for="id_cliente">Cliente:</label>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <div class="form-floating form-floating-outline">
                          <input required class="form-control" onkeyup="controlaAno(this)" type="date" id="dtInc" name="dtInc" placeholder="Início" />
                          <label for="dtInc">Início:</label>
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <div class="form-floating form-floating-outline">
                          <input required class="form-control" onkeyup="controlaAno(this)" type="date" id="dtFn" name="dtFn" placeholder="Final" />
                          <label for="dtFn">Final:</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <small class="text-light fw-medium d-block"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Modelos</font></font></small>
                        <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox1" name="mod55" value="Sim" checked>
                            <label class="form-check-label" for="inlineCheckbox1"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Modelo 55 (NFe)</font></font></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox2" name="mod65" value="Sim" checked>
                            <label class="form-check-label" for="inlineCheckbox2"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Modelo 65 (NFC-e)</font></font></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-light fw-medium d-block"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Situações Xml</font></font></small>
                        <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox4" name="statusAutorizado" checked value="Sim">
                            <label class="form-check-label" for="inlineCheckbox4"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Autorizados</font></font></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox5" name="statusCancelado" checked value="Sim">
                            <label class="form-check-label" for="inlineCheckbox5"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Cancelados</font></font></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox6" name="statusInutilizado" checked value="Sim">
                            <label class="form-check-label" for="inlineCheckbox6"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Inutilizados</font></font></label>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-4 form-group">
                        <button type="submit" class='btn btn-primary' name="button">Gerar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
