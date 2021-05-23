<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use DB;
class usuarioController extends Controller {
    public function update(Request $request) {
        try {
            $data = $request->all();
            $id = $data['id'];
            $usuario = User::find($id);

            if (password_verify($data['pass'], $usuario->clave)) {
                if ($data['passNueva'] !== 'false') {
                    $usuario->clave = Hash::make($data['passNueva']);
                }
                $usuario->usuario = $data['usuario'];
                $usuario->email = $data['email'];
                $usuario->envioCO = $data['envioCO'];
                $usuario->envioIN = $data['envioIN'];
                $usuario->envioBO = $data['envioBO'];
                
                $usuario->envioCO_usd = $data['envioCO_usd'];
                $usuario->envioIN_usd = $data['envioIN_usd'];
                $usuario->envioBO_usd = $data['envioBO_usd'];
                
                  $usuario->valor_min_cop = $data['valor_min_cop'];
                $usuario->valor_min_usd = $data['valor_min_usd'];
                
                
                $usuario->save();
            } else {
                return JsonResponse::create(array('Mensaje' => "Contraseña proporcionada es incorrecta.",'Content' =>$usuario, "isOk" => false), 200);
            }
            return JsonResponse::create(array('Mensaje' => "Usuario  Modificado Correctamente", "Content" => $usuario, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo Modificar la usuario", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }

    public function sendMessage(Request $request) {
        try {
             $data = $request->all();
             
               $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
              $cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
              $cabeceras .= $data['email']. "\r\n";  
              
              $contenido = '<br><b>Enviado por:<b>'.$data['nombre'].'<br>';  
              $contenido .= '<b>Email:<b>'.$data['email'].'<br>';  
              $contenido .= '<b>Asunto:<b>'.$data['tema'].'<br>';
              $contenido .= '<b>Mensaje:<b><br>'.$data['mensaje'].'<br>';
               
              $usuario = User::find(1);
              
              mail($usuario->email, $data['mensaje'], $contenido, $cabeceras);
   
               
           return JsonResponse::create(array('Mensaje' => "Mensaje enviado", "Content" =>$data , "isOk" => true), 200);
           } catch (Exception $exc) {
               return JsonResponse::create(array('Mensaje' => "No se pudo Modificar la usuario", "Content" => $exc->getMessage(), "isOk" => false), 401);
           }
       }


    public function getEnvio(){
        $result = DB::select("SELECT envioCO,envioIN, envioBO, envioCO_usd,envioIN_usd, envioBO_usd, valor_min_cop, valor_min_usd FROM  user WHERE id = 1 ");
        return $result;
    }

}
