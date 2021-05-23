<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Producto;
use App\Entradas;
use DB;
use File;
define('URL_SERVER', 'http://' . $_SERVER['HTTP_HOST'] . '/api/');
class ProductoController extends Controller {
    
    
    public function index() {
        
        $array_productos = array();
        $productos = DB::select("SELECT * FROM productos ORDER BY orden");
        if(count($productos)> 0){
            foreach ($productos as $key => $p) {
                $tallas = $this->get_tallas($p->id);
                $colores = $this->get_colores($p->id);
                $descuentos = $this->get_descuentos($p->id);
                $array_productos[$key] = array('propiedades' => $p, 'colores' => $colores,'tallas' => $tallas, 'descuentos' => $descuentos);
            }
        }
        return JsonResponse::create(array( "Mensaje"=>'Productos consultados...' ,"Content"=>$array_productos, "isOk" =>true), 200);
        
    }

     public function getProductosActivos() {
        
        $array_productos = array();
        $productos = DB::select("SELECT * FROM productos WHERE estado = 1 ORDER BY orden");
        if(count($productos)> 0){
            foreach ($productos as $key => $p) {
                $tallas = $this->get_tallas($p->id);
                $colores = $this->get_colores($p->id);
                $descuentos= $this->get_descuentos($p->id);
                $imagenes = $this->get_imagenes($p->id);
                $imagenes_moviles = $this->get_imagenes_moviles($p->id);
                $stock = $this->getStock($p->id);
                $array_productos[$key] = array('propiedades' => $p, 'colores' => $colores,'tallas' => $tallas, 'imagenes' => $imagenes, 'imagenes_moviles' => $imagenes_moviles, 'sotck' => $stock, 'descuentos' => $descuentos);
            }
        }
        return JsonResponse::create(array( "Mensaje"=>'Productos consultados...' ,"Content"=>$array_productos, "isOk" =>true), 200);
        
    }
    
    
     private function get_descuentos($id){
          $array = array();
        $result = DB::select("SELECT cantidad, descuento FROM producto_descuento WHERE id_producto = $id ORDER BY cantidad asc");
          if(count($result > 0)){
	        foreach ($result as $key => $i) {
	            $array[$key] = array('descuento' => $i->descuento,'cantidad' => $i->cantidad);
	        }
           
        }
        
        return $array;
    }
    
    private function get_imagenes($id){
         $array = array();
        $result = DB::select("SELECT url, color FROM producto_imagenes WHERE id_producto = $id ");
        if(count($result > 0)){
            foreach ($result as $key => $i) {
                $array[$key] = array('id' => $key, 'url' => $i->url ,'color' => $i->color );
            }

             return $array;
        }else{
            $array = '0';
        }
       
    }
    
    
     private function get_imagenes_moviles($id){
         $array = array();
        $result = DB::select("SELECT url, color FROM producto_imagenes_movil WHERE id_producto = $id ");
        if(count($result > 0)){
            foreach ($result as $key => $i) {
                $array[$key] = array('id' => $key, 'url' => $i->url ,'color' => $i->color );
            }

             return $array;
        }else{
            $array = '0';
        }
       
    }
    
    
    private function get_colores($id){
          $array = array();
        $result = DB::select("SELECT id_color, color,nombre FROM producto_color WHERE id_producto = $id ");
          if(count($result > 0)){
        foreach ($result as $key => $i) {
            $array[$key] = array('id_color' => $i->id_color ,'nombre' => $i->nombre ,'color' => $i->color);
        }
              return $array;
        }else{
            $array = '0';
        }
    }

    
    private function get_tallas($id){
          $array = array();
        $result = DB::select("SELECT talla, id_talla FROM producto_talla WHERE id_producto = $id ");
        if(count($result > 0)){
            foreach ($result as $key => $i) {
                $array[$key] = array('id_talla' => $i->id_talla, 'talla' => $i->talla);
            }
              return $array;
        }else{
            $array = '0';
        }
    }

    private function getStock($id){
            $i = DB::select("SELECT e.id_entrada,e.created_at, pc.nombre as color, pc.id_color, pt.talla, pt.id_talla, SUM(e.cantidad) as cantidad_total FROM entradas as e INNER JOIN producto_color as pc ON pc.id_color = e.id_color INNER JOIN producto_talla as pt ON pt.id_talla = e.id_talla WHERE e.id_producto = $id GROUP BY e.id_talla, e.id_color ORDER BY e.created_at");
            return $i;
    }
    
    public function store(Request $request) {
        try {
            $data = $request->all();
            $producto = (array) json_decode($data['producto']);
            
            
            $extension = $this->extension($_FILES['imagen']['name']);
            if($extension !== 'jpg' && $extension !== 'png'){
                return JsonResponse::create(array('Mensaje' => "Solo se aceptan formatos .jpg y .png : formato subido: ".$extension,"Content" => $extension, "isOk" => false), 402);
            }
            $tmpfname = time().substr(md5(microtime()), 0, rand(5, 12));
            $url = URL_SERVER . "images/productos/".$tmpfname . ".".$extension;
            $colores = $producto['colores'];
            $tallas = $producto['tallas'];
            $descuentos= $producto['descuentos'];
            $productos = new Producto();
            $productos->nombre = $producto['nombre'];
            $productos->descripcion = $producto['descripcion'];
            $productos->precio = $producto['precio'];
            $productos->precio_usd = $producto['precio_usd'];
            $productos->image = $url;
            $productos->compra_min = $producto['compra_min'];
            $productos->save();
            $request->file('imagen')->move("../images/productos", $url);
            
            
            foreach ($colores as $key => $p) {
                $arrayColores[$key] = array('id_producto' => $productos->id, 'nombre' => $p->nombre ,'color' => $p->color );
            }
            foreach ($tallas as $key => $t) {
                $arrayTallas[$key] = array('id_producto' => $productos->id, 'talla' => strtoupper($t));
            }
            
            foreach($descuentos as $key => $d) {
                $arrayDescuentos[$key] = array('id_producto' => $productos->id, 'cantidad' => $d->cantidad, 'descuento' => $d->descuento);
            }
            
            
            
            $respuesta = DB::table('producto_color')->insert($arrayColores);
            $respuesta = DB::table('producto_talla')->insert($arrayTallas);
            $respuesta = DB::table('producto_descuento')->insert($arrayDescuentos);
            
            
            
            return JsonResponse::create(array("Mensaje" => "Producto guardado correctamente", "Content" => $data, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo guardar el producto", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
    
    public function save_stock(Request $request){
        try {
        $data = $request->all();
        $entrada = new Entradas();
        $entrada->id_producto = $data['producto'];
        $entrada->id_talla = $data['talla'];
        $entrada->id_color = $data['color'];
        $entrada->cantidad = $data['cantidad'];
        $entrada->save();

        return JsonResponse::create(array("Mensaje" => "Entrada registrada", "Content" => $entrada, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo registrar entrada", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }

    public function get_stock($id){
        try {
        $i = DB::select("SELECT e.id_entrada,e.created_at, pc.nombre as color, pc.id_color, pt.talla, pt.id_talla, SUM(e.cantidad) as cantidad_total FROM entradas as e INNER JOIN producto_color as pc ON pc.id_color = e.id_color INNER JOIN producto_talla as pt ON pt.id_talla = e.id_talla WHERE e.id_producto = $id GROUP BY e.id_talla, e.id_color ORDER BY e.created_at");
        return JsonResponse::create(array("Mensaje" => "Entradas consultadas", "Content" => $i, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo consultar la entrada", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }

    private function extension($archivo){
        $partes = explode(".", $archivo);
        $extension = end($partes);
        return $extension;
    }

    public function deleteStock(Request $request) {
    	try {
    	
            $data = $request->all();
            $talla = $data['talla'];
            $color = $data['color'];
   
             DB::table('entradas')->where('id_color', $color)->where('id_talla', $talla)->update(['cantidad' => 0]);

            //DB::delete("DELETE  FROM entradas WHERE id_color = $color AND id_talla = $talla");
            
            
          
            
           
            
            return JsonResponse::create(array("Mensaje" => "Stock actualizado correctamente", "Content" => $data, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "Content" => json_encode($Encuesta)), 401);
        } 
            
    }

    public function updateStock(Request $request) {
    	try {
    	
            $data = $request->all();
            $id = $data['id'];
            $stock = $data['stock'];
   
            DB::table('producto_color')
            ->where('id_color', $id)
            ->update(['stock' => $stock]);
            
          
            
           
            
            return JsonResponse::create(array("Mensaje" => "Stock actualizado correctamente", "Content" => $data, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "Content" => json_encode($Encuesta)), 401);
        } 
            
    }
    
    public function show($id) {
        try {
            $producto = Producto::find($id);
            return JsonResponse::create(array("Mensaje" => "Consultado el producto", "Content" => $producto, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo consultar el producto", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
    
    public function update(Request $request) {
        
        try {
            $data = $request->all();
            
            
            $producto = (array) json_decode($data['producto']);
            $productos = Producto::find($producto['id']);
            
            if ($request->hasFile('imagen')) {
                $extension = $this->extension($_FILES['imagen']['name']);
                if($extension !== 'jpg' && $extension !== 'png'){
                    return JsonResponse::create(array('Mensaje' => "Solo se aceptan formatos .jpg y .png : formato subido: ".$extension,"Content" => $extension, "isOk" => false), 402);
                }
                $tmpfname = time().substr(md5(microtime()), 0, rand(5, 12));
                $url = URL_SERVER . "images/productos/".$tmpfname . ".".$extension;
            }
            
            $colores = $producto['colores'];
            $tallas = $producto['tallas'];
            $descuentos= $producto['descuentos'];
            $productos->nombre = $producto['nombre'];
            $productos->descripcion = $producto['descripcion'];
            $productos->precio = $producto['precio'];
            $productos->precio_usd = $producto['precio_usd'];
            $productos->compra_min = $producto['compra_min'];
            if ($request->hasFile('imagen')) {
                $productos->image = $url;
            }
            $productos->save();
            
            $id = $producto['id'];
            
            DB::delete("DELETE  FROM producto_talla WHERE id_producto = $id ");
            DB::delete("DELETE  FROM producto_color WHERE id_producto = $id ");
            DB::delete("DELETE  FROM producto_descuento WHERE id_producto = $id ");
            
            foreach ($colores as $key => $p) {
                     $arrayColores[$key] = array('id_producto' => $productos->id, 'nombre' => $p->nombre ,'color' => $p->color );
            }
            
            foreach ($tallas as $key => $t) {
                $arrayTallas[$key] = array('id_producto' => $productos->id, 'talla' => strtoupper($t));
            }
            
             $arrayDescuentos = array();
            foreach ($descuentos as $key => $d) {
                $arrayDescuentos[$key] = array('id_producto' => $productos->id, 'cantidad' => $d->cantidad, 'descuento' => $d->descuento);
            }
            
            
            $respuesta = DB::table('producto_color')->insert($arrayColores);
            $respuesta = DB::table('producto_talla')->insert($arrayTallas);
            $respuesta = DB::table('producto_descuento')->insert($arrayDescuentos);
            
            
            if ($request->hasFile('imagen')) {
                
                
                $res = explode("public", public_path(), 2);
                $name = explode("/", $producto['imagen'], 8);
                
                
                 $file = $res[0] . "public_html/api/images/productos/" . $name[6];
                
                if (File::isFile($file)) {
                    File::delete($file);
                }
                
                
                $request->file('imagen')->move("../images/productos", $url);
            }
            
            return JsonResponse::create(array("Mensaje" => "Producto actualizado correctamente", "Content" => json_encode($producto), "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "Error al actualizar el producto", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
    
    public function updateState(Request $request) {
        try {
            $data = $request->all();
            $id = $data['id'];
            $producto = Producto::find($id);
            $producto->estado  = $data['estado'];
            $producto->save();
            return JsonResponse::create(array("Mensaje" => "Productos actualizado correctamente", "Content" => $producto, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "Content" => json_encode($Encuesta)), 401);
        }
    }

    public function updateDisponible(Request $request) {
        try {
            $data = $request->all();
            $id = $data['id'];
            $producto = Producto::find($id);
            $producto->disponible  = $data['disponible'];
            $producto->save();
            return JsonResponse::create(array("Mensaje" => "Productos actualizado correctamente", "Content" => $producto, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "Content" => json_encode($Encuesta)), 401);
        }
    }
    
       public function updateOrden(Request $request) {
        try {
            $data = $request->all();
            $id = $data['id'];
            $producto = Producto::find($id);
            $producto->orden = $data['orden'];
            $producto->save();
            return JsonResponse::create(array("Mensaje" => "Orden actualizado correctamente", "Content" => $producto, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "Content" => json_encode($Encuesta)), 401);
        }
    }
    
    
    public function destroy($id) {
        //
        try {
        
        
        
          $producto_pedido = DB::select("SELECT id FROM producto_venta WHERE idProducto = $id");
          
          if(count($producto_pedido) > 0){
            
            return JsonResponse::create(array("Mensaje" => "No se pudo eliminar producto, por que ha sido asociado a una venta", "Content" => json_encode($id), "isOk" => true), 200);
            
          }else{
          
        
           $producto = Producto::find($id);

            $res = explode("public", public_path(), 2);
                $name = explode("/", $producto->image, 8);
                $file = $res[0] . "images\productos" . chr(92) . $name[6];
                if (File::isFile($file)) {
                    File::delete($file);
                }
            $producto->delete();
            DB::delete("DELETE  FROM producto_talla WHERE id_producto = $id ");
            DB::delete("DELETE  FROM producto_color WHERE id_producto = $id ");
           DB::delete("DELETE  FROM producto_descuento WHERE id_producto = $id ");

            $imagenes = DB::select("SELECT * FROM producto_imagenes WHERE id_producto = $id ");
            foreach ($imagenes as $key => $i) {
                $res = explode("public", public_path(), 2);
                $name = explode("/", $i->url, 8);
          
                 $file = $res[0] . "public_html/api/images/productos/" . $name[6];
                 
                if (File::isFile($file)) {
                    File::delete($file);
                }
            }
            
            


            
            
            
            DB::delete("DELETE FROM producto_imagenes WHERE id_producto = $id");
            
            
            return JsonResponse::create(array("Mensaje" => "Producto eliminado", "Content" => json_encode($producto), "isOk" => true), 200);
            
            }
            
            
        } catch (Exception $exc) {
            return JsonResponse::create(array('mMensaje' => "No se pudo eliminar el producto", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
    
    public function getImages($id){
        $result = DB::select("SELECT * FROM producto_imagenes WHERE id_producto = $id ");
        return JsonResponse::create(array("Mensaje" => "Imagenes Consultadas", "Content" => $result, "isOk" => true), 200);
    }
    
    
    
    public function getImagesMovil($id){
        $result = DB::select("SELECT * FROM producto_imagenes_movil WHERE id_producto = $id ");
        return JsonResponse::create(array("Mensaje" => "Imagenes Consultadas", "Content" => $result, "isOk" => true), 200);
    }
    
    
    
    
    public function saveImage(Request $request){
        try {
            $data = $request->all();
            $extension = $this->extension($_FILES['imagen']['name']);
            if($extension !== 'jpg' && $extension !== 'png'){
                return JsonResponse::create(array('Mensaje' => "Solo se aceptan formatos .jpg y .png : formato subido: ".$extension,"Content" => $extension, "isOk" => false), 200);
            }
            $tmpfname = time().substr(md5(microtime()), 0, rand(5, 12));
            $url = URL_SERVER . "images/productos/".$tmpfname . ".".$extension;
            $request->file('imagen')->move("../images/productos", $url);
            
            
            $id_producto = str_replace('"','',$data['producto']); 
            
            
            $array[0] = array('url' => $url,'color' => $data['color'] ,'id_producto' => $id_producto);
            $respuesta = DB::table('producto_imagenes')->insert($array);
            return JsonResponse::create(array("Mensaje" => "Imagen guardada", "Content" => $respuesta, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo guardar la imagen", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
        
    }
    
    
    public function saveImageMovil(Request $request){
        try {
            $data = $request->all();
            $extension = $this->extension($_FILES['imagen']['name']);
            if($extension !== 'jpg' && $extension !== 'png'){
                return JsonResponse::create(array('Mensaje' => "Solo se aceptan formatos .jpg y .png : formato subido: ".$extension,"Content" => $extension, "isOk" => false), 200);
            }
            $tmpfname = time().substr(md5(microtime()), 0, rand(5, 12));
            $url = URL_SERVER . "images/productos/".$tmpfname . ".".$extension;
            $request->file('imagen')->move("../images/productos", $url);
            
            
            $id_producto = str_replace('"','',$data['producto']); 
            
            
            $array[0] = array('url' => $url,'color' => $data['color'] ,'id_producto' => $id_producto);
            $respuesta = DB::table('producto_imagenes_movil')->insert($array);
            return JsonResponse::create(array("Mensaje" => "Imagen guardada", "Content" => $respuesta, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo guardar la imagen", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
        
    }
    
    
    
    public function deleteImage(Request $request){
        try {
            $data = $request->all();
            $id_imagen = $data['id'];
            $url = $data['url'];
            $res = explode("public", public_path(), 2);
            $name = explode("/", $url, 8);
            
   
            $file = $res[0] . "public_html/api/images/productos/" . $name[6];
            
        
            
            if (File::isFile($file)) {
                File::delete($file);
            }
            
          
           DB::delete("DELETE FROM producto_imagenes WHERE id = $id_imagen");
            return JsonResponse::create(array('Mensaje' => "Imagen eliminada", "Content" => $data, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo eliminar la imagen", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
    
     public function deleteImageMovil(Request $request){
        try {
            $data = $request->all();
            $id_imagen = $data['id'];
            $url = $data['url'];
            $res = explode("public", public_path(), 2);
            $name = explode("/", $url, 8);
            
   
            $file = $res[0] . "public_html/api/images/productos/" . $name[6];
            
        
            
            if (File::isFile($file)) {
                File::delete($file);
            }
            
          
           DB::delete("DELETE FROM producto_imagenes_movil WHERE id = $id_imagen");
            return JsonResponse::create(array('Mensaje' => "Imagen eliminada", "Content" => $data, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo eliminar la imagen", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
    
    
    public function updateColor(Request $request){
    try {
     $data = $request->all();
            $id_imagen = $data['imagen'];
            $color = $data['color'];
            
            
            DB::table('producto_imagenes')
            ->where('id', $id_imagen)
            ->update(['color' => $color]);
            
            
              return JsonResponse::create(array('Mensaje' => "Color Actualizado", "Content" => $data, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo actualizar el color", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    
    }
    
      public function updateColorMobil(Request $request){
    try {
     $data = $request->all();
            $id_imagen = $data['imagen'];
            $color = $data['color'];
            
            
            DB::table('producto_imagenes_movil')
            ->where('id', $id_imagen)
            ->update(['color' => $color]);
            
            
              return JsonResponse::create(array('Mensaje' => "Color Actualizado", "Content" => $data, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo actualizar el color", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    
    }
    
}