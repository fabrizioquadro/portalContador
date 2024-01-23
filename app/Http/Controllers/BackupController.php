<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Import;
use App\Models\Cliente;
use App\Models\Backup;
use App\Models\Inventario;
use App\Jobs\ProcessaXmlJob;

class BackupController extends Controller
{
    public function index(){
        //vamos buscar os clientes certinhos desse usuario
        $clientes = Cliente::listarClientesUsuario();

        return view('backups/index', compact('clientes'));
    }

    public function listar($id){
        $cliente = Cliente::where('id', $id)->first();
        $backups = Backup::where('id_cliente', $cliente->id)->orderBy('arquivo')->get();

        return view('backups/listar', compact('cliente','backups'));
    }

    public function adicionaBackup(Request $request){
        if($request->hasFile('arquivo') && $request->file('arquivo')->isValid()){
            $id_cliente = $request->get('id_cliente');

            $nm_arquivo = $request->arquivo->getClientOriginalName();

            $dados_import = [
                'id_cliente' => $id_cliente,
                'st_import' => 'Descompactar',
                'tp_import' => 'Backup',
            ];

            $import = Import::create($dados_import);

            $pasta = 'backups/'.$id_cliente."/".$import->id;

            $import->pasta = $pasta;
            $import->save();

            $request->arquivo->move(public_path($pasta), $nm_arquivo);

            ProcessaXmlJob::dispatch();
        }
    }
}
