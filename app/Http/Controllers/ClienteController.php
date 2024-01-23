<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\ClienteContador;
use App\Models\User;

class ClienteController extends Controller
{
    public function index(){
        $clientes = Cliente::all();

        return view('clientes/index', compact('clientes'));
    }

    public function adicionar(){
        $usuarios = User::listaContadores();
        return view('clientes/adicionar', compact('usuarios'));
    }

    public function insert(Request $request){
        $dados = $request->all();
        $dados['cnpj'] = cpfCnpjFormDb($dados['cnpj']);

        $cliente = Cliente::create($dados);

        $usuarios = User::listaContadores();

        foreach ($usuarios as $usuario){
            $var = "user".$usuario->id;

            if($request->get($var)){
                $dados = [
                    'id_cliente' => $cliente->id,
                    'id_user' => $usuario->id,
                ];

                ClienteContador::create($dados);
            }
        }
        return redirect()->route('clientes')->with('mensagem', 'Cliente Cadastrado');
    }

    public function editar($id){
        $cliente = Cliente::where('id', $id)->first();

        $usuarios = User::listaContadores();

        return view('clientes/editar', compact('cliente','usuarios'));
    }

    public function update(Request $request){
        $id = $request->get('id');
        $dados = $request->only('nome','fantasia','email','cnpj','tel','cel');

        Cliente::where('id', $id)->update($dados);

        ClienteContador::where('id_cliente', $id)->delete();

        $usuarios = User::listaContadores();

        foreach ($usuarios as $usuario){
            $var = "user".$usuario->id;

            if($request->get($var)){
                $dados = [
                    'id_cliente' => $id,
                    'id_user' => $usuario->id,
                ];

                ClienteContador::create($dados);
            }
        }
        return redirect()->route('clientes')->with('mensagem', 'Cliente Editado');
    }

    public function excluir($id){
        $cliente = Cliente::where('id', $id)->first();

        return view('clientes/excluir', compact('cliente'));
    }

    public function delete(Request $request){
        $id = $request->get('id');

        Cliente::where('id', $id)->delete();
        return redirect()->route('clientes')->with('mensagem', 'Cliente Excluido');
    }

    public function contadores($id){
        $cliente = Cliente::where('id', $id)->first();

        $usuarios = User::listaContadores();

        return view('clientes/contadores', compact('cliente','usuarios'));
    }

    public function contadoresSalvar(Request $request){
        $id_cliente = $request->get('id_cliente');

        ClienteContador::where('id_cliente', $id_cliente)->delete();

        $usuarios = User::listaContadores();

        foreach ($usuarios as $usuario){
            $var = "user".$usuario->id;

            if($request->get($var)){
                $dados = [
                    'id_cliente' => $id_cliente,
                    'id_user' => $usuario->id,
                ];

                ClienteContador::create($dados);
            }
        }

        return redirect()->route('clientes.contadores', $id_cliente)->with('mensagem', 'Dados Salvos com sucesso');
    }

}
