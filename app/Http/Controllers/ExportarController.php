<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Xml;
use App\Models\Import;
use App\Models\XmlProduto;
use App\Models\Cliente;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportarController extends Controller
{

    public function exportar(Request $request) {
        //$xmls = json_decode($request->get('string_ids_xmls'));
        //dd($xmls);
        $cliente = Cliente::where('id', $request->get('id_cliente'))->first();

        $cnpj = str_replace('.','',$cliente->cnpj);
        $cnpj = str_replace('/','',$cnpj);
        $cnpj = str_replace('-','',$cnpj);

        $fantasia = str_replace(' ','_',$cliente->fantasia);

        $arquivo = $cnpj.'_'.$fantasia.'.xls';
        $arquivo = str_replace(":",'h',$arquivo);

        // Configurações header para forçar o download
        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cache");
        header ("Content-type: application/x-msexcel");
        header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
        header ("Content-Description: PHP Generated Data" );
        // Envia o conteúdo do arquivo
        echo "
        <!doctype html>
        <html lang='en'>
            <head>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
                <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>

                <title>Excel</title>
            </head>
            <body>
                <div class='container-fluid'>";

                echo $request->get('dados');

                if($request->get('controle') == "vendas"){
                    echo "
                    <table>
                        <tr></tr>
                        <tr></tr>
                        <tr>
                            <th></th>
                            <th>Data</th>
                            <th>Situação</th>
                            <th>Cliente</th>
                            <th>Série</th>
                            <th>Modelo</th>
                            <th>Numero</th>
                            <th>Valor Produtos</th>
                            <th>Valor Acrescímos</th>
                            <th>Valor Descontos</th>
                            <th>Valor NF</th>
                        </tr>";

                        $cont = 0;

                        $xmls = json_decode($request->get('string_ids_xmls'));


                        foreach($xmls as $xml){
                            $cont++;
                            echo "
                            <tr>
                                <td style='font-size: 10px'>$cont</td>
                                <td style='font-size: 10px'>$xml[0]</td>
                                <td style='font-size: 10px'>$xml[1]</td>
                                <td style='font-size: 10px'>$xml[2]</td>
                                <td style='font-size: 10px'>$xml[3]</td>
                                <td style='font-size: 10px'>$xml[4]</td>
                                <td style='font-size: 10px'>$xml[5]</td>
                                <td style='font-size: 10px'>R$ $xml[7]</td>
                                <td style='font-size: 10px'>R$ $xml[8]</td>
                                <td style='font-size: 10px'>R$ $xml[9]</td>
                                <td style='font-size: 10px'>R$ $xml[10]</td>
                            </tr>";
                        }
                    }
                    else{
                        echo "
                        <table>
                            <tr></tr>
                            <tr></tr>
                            <tr>
                                <th></th>
                                <th>Data</th>
                                <th>Nr</th>
                                <th>Série</th>
                                <th>Cliente</th>
                                <th>Modelo</th>
                                <th>Chave</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>";

                            $dados = $request->get('string_ids_xmls');
                            $dados = substr($dados, 1);
                            $xmls = explode('-', $dados);
                            $cont = 0;

                            foreach ($xmls as $id_xml){
                                $xml = Xml::where('id', $id_xml)->first();
                                $cont++;
                                $var = explode(' ', $xml->dhEmi);
                                $dhEmi = dataDbForm($var[0])." ".$var[1];
                                $consumidor = $xml->destXnome != "" ? $xml->destXnome : 'Consumidor Final';
                                $valor = "R$ ".valorDbForm($xml->vPag);
                                echo "
                                <tr>
                                    <td>$cont</td>
                                    <td>$dhEmi</td>
                                    <td>$xml->numero</td>
                                    <td>$xml->serie</td>
                                    <td>$consumidor</td>
                                    <td>$xml->modDoc</td>
                                    <td>'$xml->chNFe'</td>
                                    <td>$xml->situacao</td>
                                    <td>$valor</td>
                                </tr>";
                            }
                        }

                    echo "
                    </table>
                </div>
            </body>
        </html>";
        exit();
    }

    public function downloadsXml(Request $request){
        $id_user = $request->get('id_user');
        $dados = $request->get('dados');
        $divDados = $request->get('divDados');
        $controle = $request->get('controle');
        $string_ids_xmls = $request->get('jsonXmls');
        $id_cliente = $request->get('id_cliente');

        $return = $this->gerarPdfDownloads($dados, $id_user, $divDados, $controle, $id_cliente);
        /*
        if($controle == 'vendas'){
            $return = $this->gerarPdf($string_ids_xmls, $id_user, $divDados, $controle);
        }
        else{
            $return = $this->gerarPdf($dados, $id_user, $divDados, $controle);
        }
        */

        $cliente = Cliente::where('id', $id_cliente)->first();

        $cnpj = str_replace('.','',$cliente->cnpj);
        $cnpj = str_replace('/','',$cnpj);
        $cnpj = str_replace('-','',$cnpj);

        $fantasia = str_replace(' ','_',$cliente->fantasia);

        $dados = substr($dados, 1);
        $xmls = explode('-', $dados);

        $zip = new \ZipArchive();
        $nomeArquivo = $cnpj.'_'.$fantasia.'.zip';

        $path = public_path('temp/'.$nomeArquivo);

        if (file_exists($path)){
            unlink($path);
        }

        if($zip->open($path, \ZipArchive::CREATE)){
            //vamos colocar o arquivo pdf da relação
            $file = public_path("temp/".$return['nome_arquivo']);
            $zip->addFile($file, $return['nome_arquivo']);
            foreach($xmls as $id_xml){
                $xml = Xml::where('id', $id_xml)->first();
                $import = Import::where('id', $xml->id_import)->first();
                $file = public_path($import->pasta."/".$xml->arquivo);
                $zip->addFile($file, $xml->arquivo);
            }
            //echo "<pre>";
            //print_r($zip);
            //echo "</pre>";
            $zip->close();
            $retorno['controle'] = 'true';
            $retorno['arquivoDownload'] = "/public/temp/$nomeArquivo";
        }
        else{
            $retorno['controle'] = 'false';
        }
        $retorno['teste'] = 'ok';
        echo json_encode($retorno);
    }

    public function exportarPdf(Request $request){
        $retorno = $this->gerarPdf($request->get('string_ids_xmls'), $request->get('id_user'), $request->get('divDados'), $request->get('controle'), $request->get('id_cliente'));
        echo json_encode($retorno);
    }


    public function gerarPdfDownloads($xmls, $id_user, $divDados, $controle, $id_cliente){

        $cliente = Cliente::where('id', $id_cliente)->first();

        $html = "
        <!doctype html>
        <html lang='en'>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
            <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
        </head>
        <body>
        <div class='container-fluid'>
        ";

        $html .= $divDados;

        $xmls = substr($xmls, 1);
        $xmls = explode('-', $xmls);

        if($controle == "vendas"){

            $cfops = array();
            $arrayXml = array();
            $somatorio_autorizados = 0;
            $somatorio_cancelados = 0;
            $somatorio_inutilizados = 0;
            $qt_nfe = 0;
            $qt_nfce = 0;
            $dtNfe = "0000-00-00 00:00:00";
            $dtNfce = "0000-00-00 00:00:00";
            $nrUltimaNfe = '';
            $nrUltimaNfce = '';

            foreach ($xmls as $id){
                $xml = Xml::where('id', $id)->first();


                if($xml->modDoc == "55"){
                    $qt_nfe++;
                    if(strtotime($xml->dhEmi) > strtotime($dtNfe)){
                        $dtNfe = $xml->dhEmi;
                        $nrUltimaNfe = $xml->numero;
                    }
                }
                elseif($xml->modDoc == "65"){
                    $qt_nfce++;
                    if(strtotime($xml->dhEmi) > strtotime($dtNfce)){
                        $dtNfce = $xml->dhEmi;
                        $nrUltimaNfce = $xml->numero;
                    }
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

            $somatorio_autorizados = "R$ ".valorDbForm($somatorio_autorizados);
            $somatorio_cancelados = "R$ ".valorDbForm($somatorio_cancelados);
            $somatorio_inutilizados = "R$ ".valorDbForm($somatorio_inutilizados);

            //agora vamos escrever no pdf os dados
            $html .= "
            <div class='row'>
                <div class='col-md-4'>
                    <div class='card mt-3'>
                        <div class='card-body'>
                            <h6 class='card-title'>Resumo Cfops</h6>
                            <table class='table'>
                                <thead>
                                    <tr>
                                        <th>CFOP</th>
                                        <th>VALOR</th>
                                    </tr>
                                </thead>";
                                foreach($cfops as $key => $valor){
                                    $vl = valorDbForm($valor);
                                    $html .= "
                                    <tr>
                                        <td>$key</td>
                                        <td>R$ $vl</td>
                                    </tr>
                                    ";
                                }
                                $html .= "
                            </table>
                        </div>
                    </div>
                </div>
                <div class='col-md-8'>
                    <div class='card mt-3'>
                        <div class='card-body'>
                            <h6 class='card-title'>Resumo Xmls</h6>
                            <table class='table'>
                                <thead>
                                    <tr>
                                        <th>Última NFE</th>
                                        <th>Última NFCE</th>
                                        <th>Qt NFE</th>
                                        <th>Qt NFCE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>$dtNfe - $nrUltimaNfe</td>
                                        <td>$dtNfce - $nrUltimaNfce</td>
                                        <td>$qt_nfe</td>
                                        <td>$qt_nfce</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class='card mt-3'>
                <div class='card-body'>
                    <h6 class='card-title'>Somatórios</h6>
                    <table class='table'>
                        <thead>
                            <tr>
                                <th>AUTORIZADOS</th>
                                <th>CANCELADOS</th>
                                <th>INUTILIZADOS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>$somatorio_autorizados</td>
                                <td>$somatorio_cancelados</td>
                                <td>$somatorio_inutilizados</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            ";
            //vamos colocar a tabela dos xmls
            $html .= "
            <div class='card mt-3'>
                <div class='card-body'>
                    <table class='table table-sm'>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Data</th>
                                <th>Situação</th>
                                <th>Cliente</th>
                                <th>Série</th>
                                <th>Modelo</th>
                                <th>Numero</th>
                                <th>Valor Produtos</th>
                                <th>Valor Acrescímos</th>
                                <th>Valor Descontos</th>
                                <th>Valor NF</th>
                            </tr>
                        </thead>
                        <tbody>";
                        $cont = 0;

                        foreach($arrayXml as $xml){
                            $cont++;
                            $html .= "
                            <tr>
                                <td style='font-size: 10px'>$cont</td>
                                <td style='font-size: 10px'>$xml[0]</td>
                                <td style='font-size: 10px'>$xml[1]</td>
                                <td style='font-size: 10px'>$xml[2]</td>
                                <td style='font-size: 10px'>$xml[3]</td>
                                <td style='font-size: 10px'>$xml[4]</td>
                                <td style='font-size: 10px'>$xml[5]</td>
                                <td style='font-size: 10px'>$xml[7]</td>
                                <td style='font-size: 10px'>$xml[8]</td>
                                <td style='font-size: 10px'>$xml[9]</td>
                                <td style='font-size: 10px'>$xml[10]</td>
                            </tr>
                            ";

                        }

                        $html .= "
                        </tbody>
                    </table>
                </div>
            </div>
            ";

        }
        else{

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

            foreach ($xmls as $id){
                $xml = Xml::where('id', $id)->first();

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

            $vl_total_nfe = "R$ ".valorDbForm($vl_total_nfe);
            $vl_total_nfce = "R$ ".valorDbForm($vl_total_nfce);
            $total_autorizada = "R$ ".valorDbForm($total_autorizada);
            $total_cancelada = "R$ ".valorDbForm($total_cancelada);
            $total_inutilizada = "R$ ".valorDbForm($total_inutilizada);

            $html .= "
            <div class='card mt-3'>
                <div class='card-body'>
                    <h6 class='card-title'>Somatórios Módulos</h6>
                    <table class='table'>
                        <thead>
                            <tr>
                                <th>QT NFE</th>
                                <th>QT NFCE</th>
                                <th>QT TOTAL</th>
                                <th>VALOR NFE</th>
                                <th>VALOR NFCE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>$qt_nfe</td>
                                <td>$qt_nfce</td>
                                <td>$qt_total</td>
                                <td>$vl_total_nfe</td>
                                <td>$vl_total_nfce</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class='card mt-3'>
                <div class='card-body'>
                    <h6 class='card-title'>Somatórios Situações</h6>
                    <table class='table'>
                        <thead>
                            <tr>
                                <th>QT AUTORIZADAS</th>
                                <th>QT CANCELADAS</th>
                                <th>QT INUTILIZADAS</th>
                                <th>VALOR AUTORIZADAS</th>
                                <th>VALOR CANCELADAS</th>
                                <th>VALOR INUTILIZADAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>$qt_autorizada</td>
                                <td>$qt_cancelada</td>
                                <td>$qt_inutilizada</td>
                                <td>$total_autorizada</td>
                                <td>$total_cancelada</td>
                                <td>$total_inutilizada</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class='card mt-3'>
                <div class='card-body'>
                    <table class='table'>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Data</th>
                                <th>Nr</th>
                                <th>Série</th>
                                <th>Cliente</th>
                                <th>Modelo</th>
                                <th>Chave</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>";
                        $cont = 0;
                        foreach($xmls as $id){
                            $xml = Xml::where('id', $id)->first();
                            $cont++;
                            $var = explode(' ', $xml->dhEmi);
                            $dhEmi = dataDbForm($var[0])." ".$var[1];
                            $consumidor = $xml->destXnome != "" ? $xml->destXnome : 'Consumidor Final';
                            $vPag = "R$ ".valorDbForm($xml->vPag);
                            $html .= "
                            <tr>
                                <td style='font-size: 10px'>$cont</td>
                                <td style='font-size: 10px'>$dhEmi</td>
                                <td style='font-size: 10px'>$xml->numero</td>
                                <td style='font-size: 10px'>$xml->serie</td>
                                <td style='font-size: 10px'>$consumidor</td>
                                <td style='font-size: 10px'>$xml->modDoc</td>
                                <td style='font-size: 10px'>$xml->chNFe</td>
                                <td style='font-size: 10px'>$xml->situacao</td>
                                <td style='font-size: 10px'>$vPag</td>
                            </tr>
                            ";

                        }

                        $html .= "
                        </tbody>
                    </table>
                </div>
            </div>
            ";
        }

        $html .= "
        </div>
        </body>
        </html>
        ";

        $options = new Options();
        $options->set('defaultFont', 'Courier');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html);
        $dompdf->render();

        $output = $dompdf->output();

        $cnpj = str_replace('.','',$cliente->cnpj);
        $cnpj = str_replace('/','',$cnpj);
        $cnpj = str_replace('-','',$cnpj);

        $fantasia = str_replace(' ','_',$cliente->fantasia);

        $arquivo = $cnpj.'_'.$fantasia.'.pdf';

        $file = public_path("temp/".$arquivo);

        file_put_contents($file, $output);

        $retorno['path'] = $file;
        $retorno['nome_arquivo'] = $arquivo;
        $retorno['controle'] = 'true';
        //$retorno['teste'] = $string_ids_xmls;

        return $retorno;
    }

    public function gerarPdf($string_ids_xmls, $id_user, $divDados, $controle, $id_cliente){
        $cliente = Cliente::where('id', $id_cliente)->first();

        $html = "
        <!doctype html>
        <html lang='en'>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
            <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
        </head>
        <body>
        <div class='container-fluid'>
        ";

        $html .= $divDados;

        if($controle == "vendas"){

            $html .= "
            <div class='card'>
                <div class='card-body'>
                    <table class='table table-sm'>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Data</th>
                                <th>Situação</th>
                                <th>Cliente</th>
                                <th>Série</th>
                                <th>Modelo</th>
                                <th>Numero</th>
                                <th>Valor Produtos</th>
                                <th>Valor Acrescímos</th>
                                <th>Valor Descontos</th>
                                <th>Valor NF</th>
                            </tr>
                        </thead>
                        <tbody>";
                        $cont = 0;

                        $xmls = json_decode($string_ids_xmls);

                        foreach($xmls as $xml){
                            $cont++;
                            $html .= "
                            <tr>
                                <td style='font-size: 10px'>$cont</td>
                                <td style='font-size: 10px'>$xml[0]</td>
                                <td style='font-size: 10px'>$xml[1]</td>
                                <td style='font-size: 10px'>$xml[2]</td>
                                <td style='font-size: 10px'>$xml[3]</td>
                                <td style='font-size: 10px'>$xml[4]</td>
                                <td style='font-size: 10px'>$xml[5]</td>
                                <td style='font-size: 10px'>$xml[7]</td>
                                <td style='font-size: 10px'>$xml[8]</td>
                                <td style='font-size: 10px'>$xml[9]</td>
                                <td style='font-size: 10px'>$xml[10]</td>
                            </tr>
                            ";

                        }

                        $html .= "
                        </tbody>
                    </table>
                </div>
            </div>
            ";

        }
        else{

            $string_ids_xmls = substr($string_ids_xmls, 1);

            $xmls = explode('-', $string_ids_xmls);

            $html .= "
            <div class='card'>
                <div class='card-body'>
                    <table class='table'>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Data</th>
                                <th>Nr</th>
                                <th>Série</th>
                                <th>Cliente</th>
                                <th>Modelo</th>
                                <th>Chave</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>";
                        $cont = 0;
                        foreach($xmls as $id){
                            $xml = Xml::where('id', $id)->first();
                            $cont++;
                            $var = explode(' ', $xml->dhEmi);
                            $dhEmi = dataDbForm($var[0])." ".$var[1];
                            $consumidor = $xml->destXnome != "" ? $xml->destXnome : 'Consumidor Final';
                            $vPag = "R$ ".valorDbForm($xml->vPag);
                            $html .= "
                            <tr>
                                <td style='font-size: 10px'>$cont</td>
                                <td style='font-size: 10px'>$dhEmi</td>
                                <td style='font-size: 10px'>$xml->numero</td>
                                <td style='font-size: 10px'>$xml->serie</td>
                                <td style='font-size: 10px'>$consumidor</td>
                                <td style='font-size: 10px'>$xml->modDoc</td>
                                <td style='font-size: 10px'>$xml->chNFe</td>
                                <td style='font-size: 10px'>$xml->situacao</td>
                                <td style='font-size: 10px'>$vPag</td>
                            </tr>
                            ";

                        }

                        $html .= "
                        </tbody>
                    </table>
                </div>
            </div>
            ";
        }

        $html .= "
        </div>
        </body>
        </html>
        ";

        $options = new Options();
        $options->set('defaultFont', 'Courier');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html);
        $dompdf->render();

        $output = $dompdf->output();

        $cnpj = str_replace('.','',$cliente->cnpj);
        $cnpj = str_replace('/','',$cnpj);
        $cnpj = str_replace('-','',$cnpj);

        $fantasia = str_replace(' ','_',$cliente->fantasia);

        $arquivo = $cnpj.'_'.$fantasia.'.pdf';

        $file = public_path("temp/".$arquivo);

        file_put_contents($file, $output);

        $retorno['path'] = $file;
        $retorno['nome_arquivo'] = $arquivo;
        $retorno['controle'] = 'true';
        $retorno['teste'] = $string_ids_xmls;

        return $retorno;
    }
}
