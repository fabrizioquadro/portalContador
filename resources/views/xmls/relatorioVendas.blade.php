@extends('layout')

@section('conteudo')
<style>
.h100{
    height: 100% !important;
}
</style>
<form id='formExportar' action="{{ route('exportar') }}" target='_blank' method="post">
    @csrf
    <input type="hidden" id='dadosExportar' name='dados'>
    <input type="hidden" name='id_cliente' value='{{ $cliente->id }}'>
    <input type="hidden" id="string_ids_xmls" name="string_ids_xmls" value='@php echo $jsonXmls; @endphp'>
    <input type="hidden" id="controle" name='controle' value="vendas">
</form>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="card-title">Relatório de Vendas</h5>
                </div>
                <div class="col-md-6" align='right'>
                    <button type="button" id="btnDownloads" class="btn btn-warning">Baixar Selecionados</button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown" aria-expanded="false">
                            Exportar
                        </button>
                        <ul class="dropdown-menu" style="">
                            <li><button type='button' id="btnExportarPdf" class="dropdown-item waves-effect">PDF</a></li>
                            <li><button type='button' id="btnExportarXls" class="dropdown-item waves-effect">XLS</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" align='center'>
                    <div class="col mt-3 mb-3" id='divGerandoArquivo' style='display:none'>
                        <div class="sk-fold sk-primary">
                            <div class="sk-fold-cube"></div>
                            <div class="sk-fold-cube"></div>
                            <div class="sk-fold-cube"></div>
                            <div class="sk-fold-cube"></div>
                        </div>
                        Gerando Arquivo
                    </div>
                    <div class="col mt-3 mb-3" id='divArquivoGerado' style='display:none'>
                        <a href="" id='linkArqDownload' class="btn btn-secondary" download>Baixar Arquivo Gerado</a>
                    </div>
                    <div class="col mt-3 mb-3" id='divArquivoErro' style='display:none'>
                        Ocorreu algum erro e o arquivo não pode ser gerado
                    </div>
                </div>
            </div>
            <div id="divDadosPdf">
                <div id="divDados">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th colspan="10"><b>Relatório:</b> Vendas</th>
                                        </tr>
                                        <tr>
                                            <th colspan="10"><b>Empresa:</b> {{ $cliente->nome }} - {{ $cliente->cnpj }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="10"><b>Apuração:</b> de {{ dataDbForm($dtInc) }} até {{ dataDbForm($dtFn) }}:  <b>Gerado em:</b> {{ date('d/m/Y H:i:s') }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="10"><b>Filtro:</b> {{ $filtro }}. - <b>Situações: </b> @if($filtros['situacaoA']) Autorizados @endif @if($filtros['situacaoC']) Cancelados @endif @if($filtros['situacaoI']) Inutilizados @endif</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="card h100">
                            <div class="card-body h100">
                                <h6 class="card-title">Resumo CFOPs</h6>
                                <table class='table table-sm'>
                                    <thead>
                                        <tr>
                                            <th>CFOP</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        foreach ($cfops as $key => $valor) {
                                            @endphp
                                            <tr>
                                                <td>{{ $key }}</td>
                                                <td>R$ {{ valorDbForm($valor) }}</td>
                                            </tr>
                                            @php
                                        }
                                        @endphp
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card h100">
                            <div class="card-body h100">
                                <h6 class="card-title">Resumo Xmls</h6>
                                <table class='table table-sm'>
                                    <thead>
                                        <tr>
                                            <th>Última NFE</th>
                                            <th>Última NFCE</th>
                                            <th>Quantidade NFE</th>
                                            <th>Quantidade NFCE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $ultimaNotaNfe = $controle55 == "Sim" ? $ultimasNotas['dhEmiNFe']." - Nr: ".$ultimasNotas['numeroNFe'] : '---';
                                        $ultimaNotaNfce = $controle65 == "Sim" ? $ultimasNotas['dhEmiNFCe']." - Nr: ".$ultimasNotas['numeroNFCe'] : '---';
                                        $qt_nfe = $controle55 == "Sim" ? $qt_nfe : '---';
                                        $qt_nfce = $controle65 == "Sim" ? $qt_nfce : '---';
                                        @endphp
                                        <tr>
                                            <td>{{ $ultimaNotaNfe }}</td>
                                            <td>{{ $ultimaNotaNfce }}</td>
                                            <td>{{ $qt_nfe }}</td>
                                            <td>{{ $qt_nfce }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Somatórios</h6>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Autorizados</th>
                                            <th>Cancelados</th>
                                            <th>Inutilizados</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>R$ {{ valorDbForm($somatorio_autorizados) }}</td>
                                            <td>R$ {{ valorDbForm($somatorio_cancelados) }}</td>
                                            <td>R$ {{ valorDbForm($somatorio_inutilizados) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Vendas</h5>
                    <div class="table-responsive">
                        <table class="tabela-index display" id="table-index" style='font-size: 12px'>
                            <thead>
                                <tr>
                                    <th><span><label for="checkAll">All</label><input type="checkbox" id="checkAll"></span></th>
                                    <th>Data</th>
                                    <th>Situação</th>
                                    <th>Cliente</th>
                                    <th>Serie</th>
                                    <th>Modelo</th>
                                    <th>Numero</th>
                                    <th>Valor Produtos</th>
                                    <th>Valor Acrescímos</th>
                                    <th>Valor Descontos</th>
                                    <th>Valor NF</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($arrayXml as $xml)
                                    <tr>
                                        <td> <input type="checkbox" onclick='controlaArrayDonload(this)' class='formCheck' value="{{ $xml[11] }}"> {{ ++$cont }}</td>
                                        <td>{{ $xml[0] }}</td>
                                        <td>{{ $xml[1] }}</td>
                                        <td>{{ $xml[2] }}</td>
                                        <td>{{ $xml[3] }}</td>
                                        <td>{{ $xml[4] }}</td>
                                        <td>{{ $xml[5] }}</td>
                                        <td>R$ {{ $xml[7] }}</td>
                                        <td>R$ {{ $xml[8] }}</td>
                                        <td>R$ {{ $xml[9] }}</td>
                                        <td>R$ {{ $xml[10] }}</td>
                                        <td><a href="{{ route('abrirXml', $xml[11]) }}" class="btn btn-primary btn-sm" target='_blank'>Abrir</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

var arrayXmlDownload = [];

function controlaArrayDonload(e){
    if(e.checked){
        arrayXmlDownload.push(e.value);
    }
    else{
        pos = arrayXmlDownload.indexOf(e.value);
        arrayXmlDownload.splice(pos, 1)
    }
}

document.getElementById('btnExportarPdf').addEventListener('click', ()=>{
    document.getElementById('divArquivoGerado').style.display = 'none';
    document.getElementById('divArquivoErro').style.display = 'none';
    document.getElementById('divGerandoArquivo').style.display = 'block';



    $.post(
        "{{ route('gerarPdf') }}",
        {
            string_ids_xmls : '@php echo $jsonXmls @endphp',
            id_user : {{ auth()->user()->id }},
            divDados : document.getElementById('divDadosPdf').innerHTML,
            controle : 'vendas',
            id_cliente : {{ $cliente->id }}
        },
        function(data){
            json = JSON.parse(data);

            if(json.controle == 'true'){
                document.getElementById('linkArqDownload').setAttribute('href', '/public/temp/' + json.nome_arquivo);
                document.getElementById('divGerandoArquivo').style.display = 'none';
                document.getElementById('divArquivoGerado').style.display = 'block';
                document.getElementById('divArquivoErro').style.display = 'none';
            }
            else{
                document.getElementById('divArquivoGerado').style.display = 'none';
                document.getElementById('divArquivoErro').style.display = 'block';
                document.getElementById('divGerandoArquivo').style.display = 'none';
            }
        }
    )
})

document.getElementById('btnExportarXls').addEventListener('click', ()=>{
    document.getElementById('dadosExportar').value = document.getElementById('divDadosPdf').innerHTML;
    document.getElementById('formExportar').submit();
})

document.getElementById('btnDownloads').addEventListener('click', ()=>{
    document.getElementById('divArquivoGerado').style.display = 'none';
    document.getElementById('divArquivoErro').style.display = 'none';
    document.getElementById('divGerandoArquivo').style.display = 'block';

    var controleStringEnviar = '';
    if(document.getElementById('checkAll').checked){
        controleStringEnviar = '{{ $string_ids_xmls }}';
    }
    else{
        arrayXmlDownload.forEach(function (id_xml) {
          controleStringEnviar = controleStringEnviar + '-' + id_xml;
        });
    }

    if(arrayXmlDownload.length > 0 || document.getElementById('checkAll').checked){
        $.post(
            '{{ route('downloadsXml') }}',
            {
                dados : controleStringEnviar,
                divDados : document.getElementById('divDados').innerHTML,
                id_user : {{ auth()->user()->id }},
                controle : 'vendas',
                jsonXmls : '@php echo $jsonXmls @endphp',
                id_cliente : {{ $cliente->id }}
            },
            function(data){
                json = JSON.parse(data);
                if(json.controle == "true"){
                    document.getElementById('linkArqDownload').setAttribute('href', json.arquivoDownload);
                    document.getElementById('divGerandoArquivo').style.display = 'none';
                    document.getElementById('divArquivoGerado').style.display = 'block';
                    document.getElementById('divArquivoErro').style.display = 'none';
                }
                else{
                    document.getElementById('divGerandoArquivo').style.display = 'none';
                    document.getElementById('divArquivoGerado').style.display = 'none';
                    document.getElementById('divArquivoErro').style.display = 'block';
                }
            }
        );
    }
    else{
        alert('É Necessário escolher pelo menos um xml');
        document.getElementById('divGerandoArquivo').style.display = 'none';
        document.getElementById('divArquivoGerado').style.display = 'none';
        document.getElementById('divArquivoErro').style.display = 'none';
    }





    //document.getElementById('dadosDownload').value = controleStringEnviar;
    //document.getElementById('formDownload').submit();
})
</script>
<script>
window.addEventListener('load',()=>{
  $('#table-index').DataTable({
    "ordering": false,
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
