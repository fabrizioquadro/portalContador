<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Import;
use App\Models\Xml;
use App\Models\XmlProduto;
use App\Models\EventoXml;
use App\Models\Inventario;
use App\Models\Backup;

class ProcessaXmlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //vamos buscar todos os imports que não foram finalizados
        $imports = Import::where('st_import','<>','Finalizado')
        ->where('st_import','<>','Erro')
        ->get();

        //vamos analizar as questões de descompactar os arquivos
        foreach ($imports as $import) {
            if($import->st_import == "Descompactar"){
                $zip = new \ZipArchive();
                $zip->open(public_path($import->pasta."/zipFile.zip"));

                $destino = public_path($import->pasta."/");

                if($zip->extractTo($destino) == true){
                    $st_import = 'Registrar Xml';
                }
                else{
                    $st_import = 'Erro';
                }
                Import::where('id', $import->id)->update(['st_import' => $st_import]);
            }
        }

        //vamos buscar todos os imports que não foram finalizados
        $imports = Import::where('st_import','<>','Finalizado')
        ->where('st_import','<>','Erro')
        ->get();

        //vamos analizar as questões de Registrar os Arquivos
        foreach ($imports as $import) {
            if($import->st_import == "Registrar Xml"){
                $destino = public_path($import->pasta."/");
                $arquivos = scandir($destino);

                if($import->tp_import == "Inventario"){
                    foreach($arquivos as $arquivo){
                        if($arquivo != "." && $arquivo != '..' && $arquivo != "zipFile.zip"){
                            $dados_pesquisa = [
                                'id_cliente' => $import->id_cliente,
                                'arquivo' => $arquivo,
                            ];
                            $conta = Inventario::where($dados_pesquisa)->count();
                            if($conta == 0){
                                $dados = [
                                    'id_cliente' => $import->id_cliente,
                                    'pasta' => $import->pasta,
                                    'arquivo' => $arquivo,
                                ];
                                Inventario::create($dados);
                            }
                        }
                    }
                    Import::where('id', $import->id)->update(['st_import' => 'Finalizado']);
                }
                elseif($import->tp_import == "Backup"){
                    foreach($arquivos as $arquivo){
                        if($arquivo != "." && $arquivo != '..' && $arquivo != "zipFile.zip"){
                            $dados_pesquisa = [
                                'id_cliente' => $import->id_cliente,
                                'arquivo' => $arquivo,
                            ];
                            $conta = Backup::where($dados_pesquisa)->count();

                            //vamos descobrir a data do backup
                            //$varArquivo = str_replace('BKP_','',$arquivo);
                            //$varArquivo = str_replace('.zip','',$varArquivo);
                            //$varArquivo = str_replace('.ZIP','',$varArquivo);
                            //$varArquivo = explode('_', $varArquivo);

                            $dataArquivo = date('Y-m-d H:i:s');//$varArquivo[2]."-".$varArquivo[1].'-'.$varArquivo[0]." ".$varArquivo[3].":".$varArquivo[4].":00";

                            if($conta == 0){
                                $dados = [
                                    'id_cliente' => $import->id_cliente,
                                    'pasta' => $import->pasta,
                                    'arquivo' => $arquivo,
                                    'data' => $dataArquivo,
                                ];
                                Backup::create($dados);

                                //$backups = Backup::where('id_cliente', $import->id_cliente)->orderByDesc('data')->get();
                                //$cont = 0;
                                //foreach ($backups as $backup){
                                //    $cont++;
                                //    if($cont > 4){
                                //        $arquivo = public_path($backup->pasta."/".$backup->arquivo);
                                //        unlink($arquivo);
                                //        Backup::where('id', $backup->id)->delete();
                                //    }
                                //}


                            }
                        }
                    }
                    Import::where('id', $import->id)->update(['st_import' => 'Finalizado']);
                }
                else{
                    if($import->tp_import == "Autorizados"){
                        $situacao = "A";
                    }
                    elseif($import->tp_import == "Cancelados"){
                        $situacao = "C";
                    }
                    elseif($import->tp_import == "Inutilizados"){
                        $situacao = "I";
                    }

                    foreach($arquivos as $arquivo) {
                        if($arquivo != "." && $arquivo != '..' && $arquivo != "zipFile.zip"){
                            $dados = [
                                'id_import' => $import->id,
                                'id_cliente' => $import->id_cliente,
                                'arquivo' => $arquivo,
                                'situacao' => $situacao,
                            ];
                            Xml::create($dados);
                        }
                    }
                    Import::where('id', $import->id)->update(['st_import' => 'Registrar Produtos']);
                }
            }
        }

        //vamos buscar todos os imports que não foram finalizados
        $imports = Import::where('st_import','<>','Finalizado')
        ->where('st_import','<>','Erro')
        ->get();

        //vamos analizar as questões de Registrar os Produtos
        foreach ($imports as $import) {
            if($import->st_import == "Registrar Produtos"){
                $xmls = Xml::where('id_import', $import->id)->get();
                foreach ($xmls as $linha){
                    $xml = simplexml_load_file(public_path($import->pasta."/".$linha->arquivo));

                    if(property_exists($xml, 'NFe')){
                        //vamos verificar se já foi inserido esse xml
                        $dados_pesquisa = [
                            'id_cliente' => $import->id_cliente,
                            'numero' => $xml->NFe->infNFe->ide->cNF,
                            'chNFe' => $xml->protNFe->infProt->chNFe,
                        ];

                        if(Xml::where($dados_pesquisa)->count() == 0){
                            //se entrar aqui o tp_xml será A => Autorizada
                            $dhEmi = explode('T', $xml->NFe->infNFe->ide->dhEmi);
                            $dEmi = $dhEmi[0];
                            $var = explode('-', $dhEmi[1]);
                            $hEmi = $var[0];
                            $dhEmi = $dEmi." ".$hEmi;

                            if($xml->NFe->infNFe->ide->idDest == '1'){
                                $destCpfCnpj = $xml->NFe->infNFe->dest->CNPJ;
                            }
                            else{
                                $destCpfCnpj = $xml->NFe->infNFe->dest->CPF;
                            }

                            $dados_xml = [
                                'modDoc' => $xml->NFe->infNFe->ide->mod,
                                'serie' => $xml->NFe->infNFe->ide->serie,
                                'numero' => $xml->NFe->infNFe->ide->nNF,
                                'dhEmi' => $dhEmi,
                                'destXnome' => $xml->NFe->infNFe->dest->xNome,
                                'destCpfCnpj' => $destCpfCnpj,
                                'chNFe' => $xml->protNFe->infProt->chNFe,
                                'vPag' => $xml->NFe->infNFe->pag->detPag->vPag,
                            ];

                            Xml::where('id', $linha->id)->update($dados_xml);

                            $produtos = $xml->NFe->infNFe->det;
                            foreach ($produtos as $produto) {
                                $vDesc = $produto->prod->vDesc != "" ? $produto->prod->vDesc : NULL;
                                $vOutro = $produto->prod->vOutro != "" ? $produto->prod->vOutro : NULL;
                                $dados_prod = [
                                    'id_cliente' => $linha->id_cliente,
                                    'id_xml' => $linha->id,
                                    'cProd' => $produto->prod->cProd,
                                    'cEAN' => $produto->prod->cEAN,
                                    'xProd' => $produto->prod->xProd,
                                    'NCM' => $produto->prod->NCM,
                                    'EXTIPI' => $produto->prod->EXTIPI,
                                    'CFOP' => $produto->prod->CFOP,
                                    'uCom' => $produto->prod->uCom,
                                    'qCom' => $produto->prod->qCom,
                                    'vUnCom' => $produto->prod->vUnCom,
                                    'vProd' => $produto->prod->vProd,
                                    'vDesc' => $vDesc,
                                    'vOutro' => $vOutro,
                                    'cEANTrib' => $produto->prod->cEANTrib,
                                    'uTrib' => $produto->prod->uTrib,
                                    'qTrib' => $produto->prod->qTrib,
                                    'vUnTrib' => $produto->prod->vUnTrib,
                                    'indTot' => $produto->prod->indTot,
                                ];

                                XmlProduto::create($dados_prod);
                            }
                        }
                    }
                    elseif(property_exists($xml, 'evento')){

                        $dhEmi = explode('T', $xml->evento->infEvento->dhEvento);
                        $dEmi = $dhEmi[0];
                        $var = explode('-', $dhEmi[1]);
                        $hEmi = $var[0];
                        $dhEmi = $dEmi." ".$hEmi;

                        $dados_xml = [
                            'dhEmi' => $dhEmi,
                            'chNFe' => $xml->evento->infEvento->chNFe,
                        ];

                        Xml::where('id', $linha->id)->update($dados_xml);

                        //vamos inserir na tabela de eventos do xmls
                        $dados_evento = [
                            'id_xml' => $linha->id,
                            'chNFe' => $xml->evento->infEvento->chNFe,
                            'tp_evento' => $linha->situacao,
                            'dhEvento' => $dhEmi,
                            'processamento' => 'não',
                        ];

                        EventoXml::create($dados_evento);
                    }
                    elseif(property_exists($xml, 'inutNFe')){
                        $dhEmi = explode('T', $xml->retInutNFe->infInut->dhRecbto);
                        $dEmi = $dhEmi[0];
                        $var = explode('-', $dhEmi[1]);
                        $hEmi = $var[0];
                        $dhEmi = $dEmi." ".$hEmi;

                        $dados_xml = [
                            'dhEmi' => $dhEmi,
                        ];

                        Xml::where('id', $linha->id)->update($dados_xml);

                        //vamos inserir na tabela de eventos do xmls
                        $dados_evento = [
                            'id_xml' => $linha->id,
                            'nNFIni' => $xml->retInutNFe->infInut->nNFIni,
                            'tp_evento' => $linha->situacao,
                            'dhEvento' => $dhEmi,
                            'processamento' => 'não',
                        ];

                        EventoXml::create($dados_evento);
                    }
                }

                Import::where('id', $import->id)->update(['st_import' => 'Finalizado']);
            }
        }

        //vamos fazer uma verificação se há cancelamentos para serem executados nos eventos
        $eventos = EventoXml::where('processamento', 'não')->get();

        foreach($eventos as $evento){

            $xmlEvento = Xml::where('id', $evento->id_xml)->first();

            if($evento->tp_evento == "C"){
                //vamos buscar se temos xml com a mesma chave de Controller
                //vamos buscar o dados do xml do evento
                $dados_pesquisa = [
                    'id_cliente' => $xmlEvento->id_cliente,
                    'chNFe' => $evento->chNFe,
                ];

                if(Xml::where($dados_pesquisa)->where('id','<>',$xmlEvento->id)->count() > 0){
                    $xml = Xml::where($dados_pesquisa)->where('id','<>',$xmlEvento->id)->first();

                    Xml::where('id', $xml->id)->update(['situacao' => 'C']);

                    EventoXml::where('id', $evento->id)->update(['processamento' => 'sim']);
                }
            }
            elseif($evento->tp_evento == "I"){
                $dados_pesquisa = [
                    'id_cliente' => $xmlEvento->id_cliente,
                    'numero' => $evento->nNFIni,
                ];

                if(Xml::where($dados_pesquisa)->where('id','<>',$xmlEvento->id)->count() > 0){
                    $xml = Xml::where($dados_pesquisa)->where('id','<>',$xmlEvento->id)->first();
                    $xml->situacao == 'I';

                    $xml->save();

                    EventoXml::where('id', $evento->id)->update(['processamento' => 'sim']);
                }
            }
        }


    }
}
