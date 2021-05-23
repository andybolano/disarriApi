<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Cliente;
class clienteController extends Controller
{
    public function index() {
        $data =  Cliente::All();
        return JsonResponse::create(array("Mensaje" => "Clientes consultados", "Content" => $data, "isOk" => true), 200);
    }
}
