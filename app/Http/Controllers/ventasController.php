<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use DB;
use App\Ventas;

class VentasController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     * GET/Ventas
     */
    public function index() {
       $ventas =  DB::select("SELECT ventas.*, c.nombres, c.email, c.cedula,c.pais,c.ciudad,c.direccion,c.telefono FROM ventas INNER JOIN clientes as c ON ventas.idCliente = c.id WHERE ventas.estado = 'POR REVISAR' ORDER BY ventas.transaction_date ASC");
        return JsonResponse::create(array("Mensaje" => "Ventas pendientes", "Content" => $ventas, "isOk" => true), 200);
    }


public function updateState(Request $request) {
        try {
            $data = $request->all();
            $id = $data['id'];
            $venta = Ventas::find($id);
            $venta->estado  = $data['estado'];
            $venta->save();
            return JsonResponse::create(array("Mensaje" => "Estado del pedido cambiado correctamente", "Content" => $venta, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "Descripcion", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
  
public function getByFechas($fechaInicial, $fechaFinal) {
        try {
            $ventas =  DB::select("SELECT ventas.*, c.nombres, c.email,c.pais,c.ciudad,c.direccion,c.telefono FROM ventas INNER JOIN clientes as c ON ventas.idCliente = c.id WHERE transaction_date BETWEEN '$fechaInicial' AND '$fechaFinal' AND estado IN ('DESPACHADO','ENTREGADO') ORDER BY ventas.transaction_date ASC");
            return JsonResponse::create(array("Mensaje" => "Ventas en el rango de fechas", "Content" => $ventas, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "Descripcion", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }

  public function getProductosVentas($idVenta){
        $productos =  DB::select("SELECT p.nombre,p.image, pv.idProducto, pv.color as id_color ,pv.talla as id_talla, pv.cantidad,pv.valor_unitario,pv.valor_total,pv.idVenta FROM producto_venta as pv INNER JOIN productos as p ON pv.idProducto = p.id WHERE pv.idVenta = $idVenta");
         return JsonResponse::create(array("Mensaje" => "Productos de la venta", "Content" => $productos, "isOk" => true), 200);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     * GET/Ventas/{id}
     */
    public function show($id) {
        //
        try {
            $Ventas = Ventas::find($id);
            return JsonResponse::create(array("mensaje" => "Descripcion", "request" => json_encode($Ventas), "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "request" => json_encode($id)), 401);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     * PUT/Ventas/{id}
     */
    public function update($id , Request $request) {
        //
         try {
         $data = $request->all();
         $Ventas = Ventas::find($id);
         $Ventas->campo  = $data['campo'];
         $Ventas->save();
         return JsonResponse::create(array("mensaje" => "Descripcion", "request" => json_encode($Ventas), "isOk" => true), 200);
         } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "request" => json_encode($Ventas)), 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     * DELETE/Ventas/{id}
     */
    public function destroy($id) {
        //
        try {
         $Ventas = Ventas::find($id);
          DB::delete("DELETE FROM producto_venta WHERE idVenta = $id ");
         $Ventas->delete();
         
         
         return JsonResponse::create(array("mensaje" => "Descripcion", "request" => json_encode($Ventas), "isOk" => true), 200);
         } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "request" => json_encode($Ventas)), 401);
        }
    }

}
