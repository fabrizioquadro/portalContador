<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Xml;
use App\Models\XmlProduto;
use App\Models\Import;
use App\Jobs\ProcessaXmlJob;

class XmlController extends Controller
{
    public function xml(){
        $clientes = Cliente::listarClientesUsuario();

        return view('xmls/xmlIndex', compact('clientes'));
    }

    public function vendas(){
        $clientes = Cliente::listarClientesUsuario();

        return view('xmls/vendasIndex', compact('clientes'));
    }

    public function xmlFiltrar(Request $request){
        $cliente = Cliente::where('id', $request->get('id_cliente'))->first();
        $filtros = array();

        if($request->get('mod55') == "Sim" && $request->get('mod65') == "Sim"){
            $mod = "1";
            $filtros['modulo'] = "Modelo 55 e Modelo 65";
        }
        elseif($request->get('mod55') == "Sim"){
            $mod = "2";
            $filtros['modulo'] = "Modelo 55";
        }
        elseif($request->get('mod65') == "Sim"){
            $mod = "3";
            $filtros['modulo'] = "Modelo 65";
        }
        else{
            $mod = false;
            $filtros['modulo'] = "Nenhum modulo selecionado";
        }

        $in_situacao = '';

        $filtros['situacaoA'] = false;
        $filtros['situacaoC'] = false;
        $filtros['situacaoI'] = false;

        if($request->get('statusAutorizado') == "Sim"){
            $in_situacao .= ",'A'";
            $filtros['situacaoA'] = "Sim";
        }

        if($request->get('statusCancelado') == "Sim"){
            $in_situacao .= ",'C'";
            $filtros['situacaoC'] = "Sim";
        }

        if($request->get('statusInutilizado') == "Sim"){
            $in_situacao .= ",'I'";
            $filtros['situacaoI'] = "Sim";
        }

        if($in_situacao){
            $in_situacao = substr($in_situacao,1);
        }


        $xmls = Xml::listarRelacaoXml($cliente->id, $request->get('dtInc'), $request->get('dtFn'), $mod, $in_situacao);

        //vamos contar a quantidade e os valores das notas
        $qt_nfe = 0;
        $qt_nfce = 0;
        $qt_total = 0;
        $qt_autorizada = 0;
        $qt_cancelada = 0;
        $qt_inutilizada = 0;
        $total_autorizada = 0;
        $total_cancelada = 0;
        $total_inutilizada = 0;
        $vl_total_nfe = 0;
        $vl_total_nfce = 0;
        $string_ids_xmls = "";

        foreach($xmls as $xml){
            $string_ids_xmls .= "-".$xml->id;
            $qt_total++;
            if($xml->modDoc == "55"){
                $qt_nfe++;
                $vl_total_nfe += $xml->vPag;
            }
            else{
                $qt_nfce++;
                $vl_total_nfce += $xml->vPag;
            }

            if($xml->situacao == "A"){
                $qt_autorizada++;
                $total_autorizada += $xml->vPag;
            }
            elseif($xml->situacao == "C"){
                $qt_cancelada++;
                $total_cancelada += $xml->vPag;
            }
            elseif($xml->situacao == "I"){
                $qt_inutilizada++;
                $total_inutilizada += $xml->vPag;
            }
        }

        $cont = 0;
        $somatorio = 0;
        $dtInc = $request->get('dtInc');
        $dtFn = $request->get('dtFn');
        return view('xmls/relacaoNfe', compact('cliente','dtInc','dtFn','xmls','cont','somatorio','filtros','qt_nfe','qt_nfce','qt_total','vl_total_nfe','vl_total_nfce','qt_autorizada','qt_cancelada','qt_inutilizada','total_autorizada','total_cancelada','total_inutilizada','string_ids_xmls'));

    }

    public function vendasFiltrar(Request $request){
        $dtInc = $request->get('dtInc');
        $dtFn = $request->get('dtFn');

        $cliente = Cliente::where('id', $request->id_cliente)->first();

        //vamos buscar todos os xmls desse periodo
        if($request->get('mod55') == "Sim" && $request->get('mod65') == "Sim"){
            $mod = "1";
            $controle55 = 'Sim';
            $controle65 = 'Sim';
            $filtro = 'Modelo 55, Modelo 65';
        }
        elseif($request->get('mod55') == "Sim"){
            $mod = '2';
            $controle55 = 'Sim';
            $controle65 = 'Não';
            $filtro = "Modelo 55";
        }
        elseif($request->get('mod65') == "Sim"){
            $mod = '3';
            $controle55 = 'Não';
            $controle65 = 'Sim';
            $filtro = 'Modelo 65';
        }
        else{
            $mod = false;
            $controle55 = 'Sim';
            $controle65 = 'Sim';
            $filtro = 'Sem Filtro';
        }

        $in_situacao = '';

        $filtros['situacaoA'] = false;
        $filtros['situacaoC'] = false;
        $filtros['situacaoI'] = false;

        if($request->get('statusAutorizado') == "Sim"){
            $in_situacao .= ",'A'";
            $filtros['situacaoA'] = "Sim";
        }

        if($request->get('statusCancelado') == "Sim"){
            $in_situacao .= ",'C'";
            $filtros['situacaoC'] = "Sim";
        }

        if($request->get('statusInutilizado') == "Sim"){
            $in_situacao .= ",'I'";
            $filtros['situacaoI'] = "Sim";
        }

        if($in_situacao){
            $in_situacao = substr($in_situacao,1);
        }

        $xmls = Xml::listarRelacaoVendas($cliente->id, $request->get('dtInc'), $request->get('dtFn'), $mod, $in_situacao);

        $ultimasNotas = Xml::listarUltimasXml($cliente->id, $request->get('dtInc'), $request->get('dtFn'));

        $cfops = array();
        $arrayXml = array();
        $somatorio_autorizados = 0;
        $somatorio_cancelados = 0;
        $somatorio_inutilizados = 0;
        $qt_nfe = 0;
        $qt_nfce = 0;
        $string_ids_xmls = '';

        foreach ($xmls as $xml){

            $string_ids_xmls .= '-'.$xml->id;

            if($xml->modDoc == "55"){
                $qt_nfe++;
            }
            elseif($xml->modDoc == "65"){
                $qt_nfce++;
            }

            if($xml->situacao == "A"){
                $somatorio_autorizados += $xml->vPag;

                $var = explode(' ', $xml->dhEmi);
                $dhEmi = dataDbForm($var[0])." ".$var[1];

                //vamos buscar os produtos do xmls
                $produtos = XmlProduto::where('id_xml', $xml->id)->get();
                $vXml = 0;
                $vOutro = 0;
                $vDesc = 0;

                foreach($produtos as $produto){
                    $cfop = $produto->CFOP;

                    if(array_key_exists($cfop, $cfops)){
                        $cfops[$cfop] = $cfops[$cfop] + $produto->vProd + $produto->vOutro - $produto->vDesc;
                    }
                    else{
                        $cfops[$cfop] = $produto->vProd + $produto->vOutro - $produto->vDesc;
                    }

                    $vXml += $produto->vProd;
                    $vOutro += $produto->vOutro;
                    $vDesc += $produto->vDesc;
                }

                $array = array();
                $array[] = $dhEmi;
                $array[] = 'A';
                $array[] = $xml->destXnome != "" ? $xml->destXnome : "Consumidor Final";
                $array[] = $xml->serie;
                $array[] = $xml->modDoc;
                $array[] = $xml->numero;
                $array[] = $xml->chNFe;
                $array[] = valorDbForm($vXml);
                $array[] = valorDbForm($vOutro);
                $array[] = valorDbForm($vDesc);
                $array[] = valorDbForm($vXml + $vOutro - $vDesc);
                $array[] = $xml->id;

                $arrayXml[] = $array;
            }
            elseif($xml->situacao == "C"){
                $array = array();
                $array[] = $dhEmi;
                $array[] = 'C';
                $array[] = $xml->destXnome != "" ? $xml->destXnome : "Consumidor Final";
                $array[] = $xml->serie;
                $array[] = $xml->modDoc;
                $array[] = $xml->numero;
                $array[] = $xml->chNFe;
                $array[] = '0,00';
                $array[] = '0,00';
                $array[] = '0,00';
                $array[] = valorDbForm($xml->vPag);
                $array[] = $xml->id;

                $arrayXml[] = $array;

                $somatorio_cancelados += $xml->vPag;
            }
            elseif($xml->situacao == "I"){
                $array = array();
                $array[] = $dhEmi;
                $array[] = 'C';
                $array[] = $xml->destXnome != "" ? $xml->destXnome : "Consumidor Final";
                $array[] = $xml->serie;
                $array[] = $xml->modDoc;
                $array[] = $xml->numero;
                $array[] = $xml->chNFe;
                $array[] = '0,00';
                $array[] = '0,00';
                $array[] = '0,00';
                $array[] = valorDbForm($xml->vPag);
                $array[] = $xml->id;

                $arrayXml[] = $array;

                $somatorio_inutilizados += $xml->vPag;
            }
        }

        $cont = 0;
        $somaXml = 0;
        $somaOutro = 0;
        $somaDesc = 0;
        $somaTotal = 0;

        $jsonXmls = json_encode($arrayXml);

        $var = explode(' ',$ultimasNotas['dhEmiNFe']);
        $ultimasNotas['dhEmiNFe'] = $var[0];
        $var = explode(' ',$ultimasNotas['dhEmiNFCe']);
        $ultimasNotas['dhEmiNFCe'] = $var[0];

        return view('xmls/relatorioVendas', compact('dtInc','dtFn','cliente','cfops','arrayXml','somatorio_autorizados','somatorio_cancelados','somatorio_inutilizados','cont','somaXml','somaDesc','somaOutro','somaTotal','qt_nfe','qt_nfce','ultimasNotas','controle55','controle65','filtro','filtros','string_ids_xmls','jsonXmls'));
    }

    public function meses($id){
        $cliente = Cliente::where('id', $id)->first();
        //vamos buscar os meses que possui o cliente
        $distincts = Xml::where('id_cliente', $id)
            ->select('mes_ano')
            ->distinct()
            ->orderByDesc('ano')
            ->orderByDesc('mes')
            ->get();

        return view('xmls/meses', compact('distincts','cliente'));
    }

    public function adicionaXml(Request $request){
        if($request->hasFile('arquivo_autorizados') && $request->file('arquivo_autorizados')->isValid()){
            $id_cliente = $request->id_cliente;
            $mes = $request->mes;
            $ano = $request->ano;
            $nm_arquivo = $request->arquivo_autorizados->getClientOriginalName();

            $dados_import = [
                'id_cliente' => $id_cliente,
                'st_import' => 'Descompactar',
                'tp_import' => 'Autorizados',
            ];

            $import = Import::create($dados_import);

            $pasta = 'xmls/'.$id_cliente."/".$import->id;

            $import->pasta = $pasta;
            $import->save();

            $request->arquivo_autorizados->move(public_path($pasta), $nm_arquivo);

        }

        if($request->hasFile('arquivo_cancelados') && $request->file('arquivo_cancelados')->isValid()){
            $id_cliente = $request->id_cliente;
            $mes = $request->mes;
            $ano = $request->ano;
            $nm_arquivo = $request->arquivo_cancelados->getClientOriginalName();

            $dados_import = [
                'id_cliente' => $id_cliente,
                'st_import' => 'Descompactar',
                'tp_import' => 'Cancelados',
            ];

            $import = Import::create($dados_import);

            $pasta = 'xmls/'.$id_cliente."/".$import->id;

            $import->pasta = $pasta;
            $import->save();

            $request->arquivo_cancelados->move(public_path($pasta), $nm_arquivo);

        }

        if($request->hasFile('arquivo_inutilizados') && $request->file('arquivo_inutilizados')->isValid()){
            $id_cliente = $request->id_cliente;
            $mes = $request->mes;
            $ano = $request->ano;
            $nm_arquivo = $request->arquivo_inutilizados->getClientOriginalName();

            $dados_import = [
                'id_cliente' => $id_cliente,
                'st_import' => 'Descompactar',
                'tp_import' => 'Inutilizados',
            ];

            $import = Import::create($dados_import);

            $pasta = 'xmls/'.$id_cliente."/".$import->id;

            $import->pasta = $pasta;
            $import->save();

            $request->arquivo_inutilizados->move(public_path($pasta), $nm_arquivo);

        }

        ProcessaXmlJob::dispatch();
    }

    public function abrirXml($id){
        $xml = Xml::where('id', $id)->first();
        $import = Import::where('id', $xml->id_import)->first();

        $arquivo = "/public/$import->pasta/$xml->arquivo";

        echo "<script> window.location.href = '$arquivo'; </script>";

    }

}
