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
            $user = DB::table('user')->where('usuario', $data['username'])->first();
               if(count($user) == 1){
                   if(password_verify($data['password'], $user->clave)){
                          return JsonResponse::create(array( "Content"=>$user, "isOk" =>true), 200);
                   }else{
                       return JsonResponse::create(array('Mensaje' => "Datos de autenticacion Incorrectos", "isOk" =>false), 200); 
                   }
               }else{
                   return JsonResponse::create(array('Mensaje' => "Usuario ".$data['username']." no existe", "isOk" =>false), 200);
               }
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No pudimos autenticarte, intentalo de nuevo", "exception" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
   


    public function logout($id){
     try {
       $user = User::find($id);
       $user->remember_token = "";
       $user->save();
       return "Logout";
       } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No pudimos cerrar la sesión, intentelo de nuevo", "exception" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
    
   
}