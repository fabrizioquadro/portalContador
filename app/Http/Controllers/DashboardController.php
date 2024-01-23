<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cliente;
use App\Models\ClienteContador;

class DashboardController extends Controller
{
    public function index(){
        if(auth()->user()->tipo == "Administrador"){
            $clientes = Cliente::listarClientesUsuario();
            $qtClientes = count($clientes);

            $qtUsuarios = User::all()->count();
            return view('dashboard/index', compact('clientes','qtClientes','qtUsuarios'));
        }
        else{
            //vamos buscar o numero de clientes que o contador possui
            $clientes = Cliente::listarClientesUsuario();
            $qtClientes = count($clientes);
            return view('dashboard/indexContador', compact('qtClientes', 'clientes'));
        }

    }

    public function perfil(){
        return view('dashboard/perfil');
    }

    public function perfilUpdate(Request $request){
        $id = auth()->user()->id;
        if(auth()->user()->tipo == "Administrador"){
            $dados = $request->only('nome','email','tipo','genero');
        }
        else{
            $dados = $request->only('nome','email','genero');
        }

        User::where('id', $id)->update($dados);

        if($request->hasFile('imagem') && $request->file('imagem')->isValid()){
            $imagem = $request->imagem;
            $extensao = $imagem->extension();

            $nmImagem = $id.".".$extensao;
            $dadosUpdate['imagem'] = $nmImagem;

            $request->imagem->move(public_path('img/usuarios'), $nmImagem);

            User::where('id', $id)->update($dadosUpdate);
        }

        return redirect()->route('perfil')->with('mensagem', "Perfil Alterado");
    }

    public function alterarSenha(){
        return view('dashboard/alterarSenha');
    }

    public function alterarSenhaUpdate(Request $request){
        $dados['password'] = bcrypt($request->get('password'));
        User::where('id', auth()->user()->id)->update($dados);

        return redirect()->route('perfil')->with('mensagem', "Senha Alterada");
    }

}
