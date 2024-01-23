<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\ClienteContador;
use App\Models\Xml;
use App\Models\Import;

if(!function_exists('createPassword')){
    function createPassword($tamanho, $maiusculas, $minusculas, $numeros, $simbolos){
        $senha = "";
        $ma = "ABCDEFGHIJKLMNOPQRSTUVYXWZ"; // $ma contem as letras maiúsculas
        $mi = "abcdefghijklmnopqrstuvyxwz"; // $mi contem as letras minusculas
        $nu = "0123456789"; // $nu contem os números
        $si = "!@#$%¨&*()_+="; // $si contem os símbolos

        if ($maiusculas){
            // se $maiusculas for "true", a variável $ma é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($ma);
        }

        if ($minusculas){
            // se $minusculas for "true", a variável $mi é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($mi);
        }

        if ($numeros){
            // se $numeros for "true", a variável $nu é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($nu);
        }

        if ($simbolos){
            // se $simbolos for "true", a variável $si é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($si);
        }

        // retorna a senha embaralhada com "str_shuffle" com o tamanho definido pela variável $tamanho
        return substr(str_shuffle($senha),0,$tamanho);

    }
}


if(!function_exists('dataDbForm')){
    function dataDbForm($data){
        $data = explode("-", $data);
        $data = $data[2]."/".$data[1]."/".$data[0];
        return $data;
    }
}


if(!function_exists('valorFormDb')){
    function valorFormDb($valor){
        //vamos procurar se foi digitado a ,
        $virgula = strpos($valor, ',');

        if($virgula === false){
            $valor = str_replace(".","",$valor);
            $valor = $valor.".00";
            return $valor;
        }

        $var = explode(',', $valor);
        $variavel = $var[1];
        $var = str_replace('.', '', $var[0]);
        $valor = $var.'.'.$variavel[0].$variavel[1];
        return $valor;
    }
}


if(!function_exists('valorDbForm')){
    function valorDbForm($valor){
        return number_format($valor,2,",",".");
    }
}


if(!function_exists('enviarMail')){
    function enviarMail($destinatario, $assunto, $mensagem){
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->setLanguage('br');
            $mail->CharSet = "utf8";
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tiojoca@webpel.dev.br';
            $mail->Password = 'P&dr0Quadr0';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->FromName = "Portal Contador";
            $mail->From = "tiojoca@webpel.dev.br";
            $mail->IsHTML(true);
            $mail->Subject = $assunto;
            $mail->Body = $mensagem;
            $mail->AddAddress($destinatario);
            $mail->Send();
        }
        catch (Exception $e) {
            $this->errorInfo = $mail->ErrorInfo;
        }
    }
}

if(!function_exists('calculaTempoOperacao')){
      function calculaTempoOperacao($dtHrEntrada, $dtHrSaida){
          $tempoSegundo = strtotime($dtHrSaida) - strtotime($dtHrEntrada);
          $tempoHoras = intdiv($tempoSegundo, 3600);
          $resto = $tempoSegundo % 3600;

          if($resto > 60){
              $tempoMinutos = intdiv($resto, 60);
          }
          else{
              $tempoMinutos = 0;
          }

          $retorno = "";

          if($tempoHoras > 0){
              $retorno .= $tempoHoras." hora(s) ";
          }

          if($tempoMinutos > 0){
              $retorno .= $tempoMinutos." minuto(s)";
          }

          return $retorno;

      }
}

if(!function_exists('cpfCnpjFormDb')){
    function cpfCnpjFormDb($var){
        $retorno = "";
        $var = str_split($var);
        foreach($var as $l){
            if(is_numeric($l)){
                $retorno .= $l;
            }
        }
        return $retorno;
    }
}

if(!function_exists('cpfCnpjDbForm')){
    function cpfCnpjDbForm($var){
        $contador = strlen($var);
        if($contador == 11){
            $retorno = $var[0].$var[1].$var[2].".".$var[3].$var[4].$var[5].".".$var[6].$var[7].$var[8]."-".$var[9].$var[10];
        }
        elseif($contador == 14){
            $retorno = $var[0].$var[1].".".$var[2].$var[3].$var[4].".".$var[5].$var[6].$var[7]."/".$var[8].$var[9].$var[10].$var[11]."-".$var[12].$var[13];
        }
        else{
            $retorno = $var;
        }
        return $retorno;
    }
}

if(!function_exists('verificaClienteContador')){
    function verificaClienteContador($id_cliente, $id_user){
        return ClienteContador::verificaClienteContador($id_cliente, $id_user);
    }
}

if(!function_exists('buscaUltimoImportCliente')){
    function buscaUltimoImportCliente($id_cliente){
        $import = Import::where('id_cliente', $id_cliente)->orderByDesc('created_at')->first();
        if($import){
            $var = explode(' ', $import->created_at);
            return dataDbForm($var[0])." ".$var[1];
        }
        else{
            return "Este cliente não possui envio de xmls";
        }
    }
}


?>
