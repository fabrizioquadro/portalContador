@extends('layout')

@section('conteudo')

<form id='formExportar' action="{{ route('exportar') }}" target='_blank' method="post">
    @csrf
    <input type="hidden" id='dadosExportar' name='dados'>
    <input type="hidden" id="string_ids_xmls" name="string_ids_xmls" value="{{ $string_ids_xmls }}">
    <input type="hidden" id="controle" value="xls">
    <input type="hidden" name='id_cliente' value='{{ $cliente->id }}'>
</form>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="card-title">Relação de Xml</h5>
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
                <div id='divDadosPdf'>
                    <div id='divDados'>
                        <div class="card">
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th colspan="10"><b>Relatório:</b> Relação de Xml</th>
                                    </tr>
                                    <tr>
                                        <th colspan="10"><b>Empresa:</b> {{ $cliente->nome }} - {{ $cliente->cnpj }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="10"><b>Apuração:</b> de {{ dataDbForm($dtInc) }} até {{ dataDbForm($dtFn) }} <b>Gerado em: </b> {{ date('d/m/Y H:i:s') }} </th>
                                    </tr>
                                    <tr>
                                        <th colspan="10"><b>Filtro: </b> {{ $filtros['modulo'] }}. - <b>Situações: </b> @if($filtros['situacaoA']) Autorizados @endif @if($filtros['situacaoC']) Cancelados @endif @if($filtros['situacaoI']) Inutilizados @endif</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">Somatórios Modulos</h6>
                        <table class='table'>
                            <thead>
                                <tr>
                                    <th>Qt NFe</th>
                                    <th>Qt NFCe</th>
                                    <th>Qt Total</th>
                                    <th>Valor NFe</th>
                                    <th>Valor NFCe</th>
                                </tr>
                                <tr>
                                    <td>{{ $qt_nfe }}</td>
                                    <td>{{ $qt_nfce }}</td>
                                    <td>{{ $qt_total }}</td>
                                    <td>R$ {{ valorDbForm($vl_total_nfe) }}</td>
                                    <td>R$ {{ valorDbForm($vl_total_nfce) }}</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">Somatórios Situações</h6>
                        <table class='table'>
                            <thead>
                                <tr>
                                    <th>Qt Autorizados</th>
                                    <th>Qt Cancelados</th>
                                    <th>Qt Inutilizados</th>
                                    <th>Valor Autorizados</th>
                                    <th>Valor Cancelados</th>
                                    <th>Valor Inutilizados</th>
                                </tr>
                                <tr>
                                    <td>{{ $qt_autorizada }}</td>
                                    <td>{{ $qt_cancelada }}</td>
                                    <td>{{ $qt_inutilizada }}</td>
                                    <td>R$ {{ valorDbForm($total_autorizada) }}</td>
                                    <td>R$ {{ valorDbForm($total_cancelada) }}</td>
                                    <td>R$ {{ valorDbForm($total_inutilizada) }}</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="tabela-index display" id="table-index" style='font-size: 12px'>
                            <thead>
                                <tr>
                                    <th><span><label for="checkAll">All</label><input type="checkbox" id="checkAll"></span></th>
                                    <th>Data</th>
                                    <th>Nr</th>
                                    <th>Série</th>
                                    <th>Cliente</th>
                                    <th>Modelo</th>
                                    <th>Chave</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($xmls as $xml)
                                    @php
                                    $cont++;
                                    $var = explode(' ', $xml->dhEmi);
                                    $dhEmi = dataDbForm($var[0])." ".$var[1];
                                    if($xml->situacao == "A"){
                                        $somatorio += $xml->vPag;
                                    }
                                    $consumidor = $xml->destXnome != "" ? $xml->destXnome : 'Consumidor Final';
                                    @endphp
                                    <tr>
                                        <td><input type="checkbox" onclick='controlaArrayDonload(this)' class='formCheck' value="{{ $xml->id }}"> {{ $cont }}</td>
                                        <td>{{ $dhEmi }}</td>
                                        <td>{{ $xml->numero }}</td>
                                        <td>{{ $xml->serie }}</td>
                                        <td>{{ $consumidor }}</td>
                                        <td>{{ $xml->modDoc }}</td>
                                        <td style='font-size: 8px'>'{{ $xml->chNFe }}'</td>
                                        <td>{{ $xml->situacao }}</td>
                                        <td>{{ 'R$ '.valorDbForm($xml->vPag) }}</td>
                                        <td> <a href="{{ route('abrirXml', $xml->id) }}" class="btn btn-primary btn-sm" target='_blank'>Abrir</a> </td>
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

document.getElementById('btnExportarPdf').addEventListener('click', ()=>{
    document.getElementById('divArquivoGerado').style.display = 'none';
    document.getElementById('divArquivoErro').style.display = 'none';
    document.getElementById('divGerandoArquivo').style.display = 'block';

    $.post(
        "{{ route('gerarPdf') }}",
        {
            string_ids_xmls : document.getElementById('string_ids_xmls').value,
            id_user : {{ auth()->user()->id }},
            divDados : document.getElementById('divDadosPdf').innerHTML,
            controle : 'download',
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

function controlaArrayDonload(e){
    if(e.checked){
        arrayXmlDownload.push(e.value);
    }
    else{
        pos = arrayXmlDownload.indexOf(e.value);
        arrayXmlDownload.splice(pos, 1)
    }
}

document.getElementById('btnDownloads').addEventListener('click', ()=>{
    document.getElementById('divArquivoGerado').style.display = 'none';
    document.getElementById('divArquivoErro').style.display = 'none';
    document.getElementById('divGerandoArquivo').style.display = 'block';

    var controleStringEnviar = '';
    if(document.getElementById('checkAll').checked){
        controleStringEnviar = document.getElementById('string_ids_xmls').value;
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
