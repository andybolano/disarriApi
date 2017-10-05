<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\User;
use DB;
class AuthenticateController extends Controller
{
        
      public function authenticate(Request $request){
      $data = $request->all();
        try {
            $user = DB::table('user')->where('usuario', $data['usuario'])->first();
               if(count($user) == 1){
                   if(password_verify($data['password'], $user->clave)){
                          return JsonResponse::create(array("user"=>json_encode($user), "respuesta" =>true), 200);
                   }else{
                       return JsonResponse::create(array('message' => "Datos de autenticacion Incorrectos", "respuesta" =>false), 200); 
                   }
               }else{
                   return JsonResponse::create(array('message' => "Usuario ".$data['usuario']." no existe", "respuesta" =>false), 200);
               }
        } catch (Exception $exc) {
            return JsonResponse::create(array('message' => "No pudimos autenticarte, intentelo de nuevo", "exception" => $exc->getMessage(), "respuesta" => false), 401);
        }
    }
   


    public function logout(Request $request){
     try {
       $user = User::find($id);
       $user->remember_token = "";
       $user->save();
       return "Logout";
       } catch (Exception $exc) {
            return JsonResponse::create(array('message' => "No pudimos cerrar la sesión, intentelo de nuevo", "exception" => $exc->getMessage(), "respuesta" => false), 401);
        }
    }
    
   
}