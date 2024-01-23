<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Import;
use App\Models\Cliente;
use App\Models\Inventario;
use App\Jobs\ProcessaXmlJob;

class InventarioController extends Controller
{
    public function index(){
        //vamos buscar os clientes certinhos desse usuario
        $clientes = Cliente::listarClientesUsuario();

        return view('inventarios/index', compact('clientes'));
    }

    public function listar($id){
        $cliente = Cliente::where('id', $id)->first();
        $inventarios = Inventario::where('id_cliente', $cliente->id)->orderBy('arquivo')->get();

        return view('inventarios/listar', compact('cliente','inventarios'));
    }

    public function adicionaInventario(Request $request){
        if($request->hasFile('arquivos_inventario') && $request->file('arquivos_inventario')->isValid()){
            $id_cliente = $request->get('id_cliente');

            $nm_arquivo = $request->arquivos_inventario->getClientOriginalName();

            $dados_import = [
                'id_cliente' => $id_cliente,
                'st_import' => 'Descompactar',
                'tp_import' => 'Inventario',
            ];

            $import = Import::create($dados_import);

            $pasta = 'inventarios/'.$id_cliente."/".$import->id;

            $import->pasta = $pasta;
            $import->save();

            $request->arquivos_inventario->move(public_path($pasta), $nm_arquivo);

            ProcessaXmlJob::dispatch();
        }
    }
}
