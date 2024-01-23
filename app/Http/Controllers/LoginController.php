<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function index(){
        return view('login/index');
    }

    public function login(Request $request){
        $dados = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if(Auth::attempt($dados)){
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }
        else{
          return redirect()->back()->with('erro', "Email ou senha inválidos");
        }
    }

    public function esqueceuSenha(){
        return view('login/esqueceuSenha');
    }

    public function recuperarSenha(Request $request){
        $email = $request->email;

        //echo $email;
        $user = User::where('email', $email)->first();

        if($user){
            $novaSenha = createPassword(8, true, true, true, false);

            $user->password = bcrypt($novaSenha);

            $user->save();

            $mensagem = "
            <h4>Nova Senha de Acesso</h4>
            <p>
                Foi alterado por sua solicitação a senha de acesso ao sistema da Samart Money Makers.
            </p>
            <p>
                Sua nova senha é: $novaSenha
            </p>
            ";

            enviarMail($user->email, 'Nova Senha de Acesso', $mensagem);

            return redirect()->route('index')->with('mensagem','Sua nova senha foi enviado para o seu email.');
        }
        else{
            return redirect()->back()->with('mensagem', "Email Inválido");
        }
    }

    public function logout(){
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('index');
    }

}
