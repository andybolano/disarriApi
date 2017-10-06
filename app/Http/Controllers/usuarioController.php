<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class usuarioController extends Controller {
    //
    public function update($id, Request $request) {
        try {
            $data = $request->all();
            $usuario = User::find($id);

            if (password_verify($data['pass'], $usuario->clave)) {
                if ($data['passNueva']) {
                    $usuario->clave = Hash::make($data['passNueva']);
                }
                $usuario->usuario = $data['usuario'];
                $usuario->email = $data['email'];
                $usuario->save();
            } else {
                return JsonResponse::create(array('mensaje' => "Contraseña proporcionada es incorrecta.", "isOk" => false), 200);
            }
            return JsonResponse::create(array('mensaje' => "Usuario  Modificado Correctamente", "request" => json_encode($usuario), "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "No se pudo Modificar la usuario", "exception" => $exc->getMessage(), "request" => json_encode($usuario), "isOk" => false), 401);
        }
    }

}
