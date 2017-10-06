<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Producto;

class ProductoController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     * GET/producto
     */
    public function index() {
        return Producto::All();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     * POST/producto
     */
    public function store(Request $request) {
        try {
            $data = $request->all();
            $producto = new Producto();
            $producto->campo = $data['campo'];
            $producto->save();
            return JsonResponse::create(array("mensaje" => "Descripcion", "request" => json_encode($data), "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "request" => json_encode($data)), 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     * GET/producto/{id}
     */
    public function show($id) {
        //
        try {
            $producto = Producto::find($id);
            return JsonResponse::create(array("mensaje" => "Descripcion", "request" => json_encode($producto), "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "request" => json_encode($id)), 401);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     * PUT/producto/{id}
     */
    public function update($id , Request $request) {
        //
         try {
         $data = $request->all();
         $producto = Producto::find($id);
         $producto->campo  = $data['campo'];
         $producto->save();
         return JsonResponse::create(array("mensaje" => "Descripcion", "request" => json_encode($producto), "isOk" => true), 200);
         } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "request" => json_encode($producto)), 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     * DELETE/producto/{id}
     */
    public function destroy($id) {
        //
        try {
         $producto = Producto::find($id);
         $producto->delete();
         return JsonResponse::create(array("mensaje" => "Descripcion", "request" => json_encode($producto), "isOk" => true), 200);
         } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "request" => json_encode($producto)), 401);
        }
    }

}