<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Coleccion;
use DB;
use File;
define('URL_SERVER', 'http://' . $_SERVER['HTTP_HOST'] . '/api/');

class coleccionesController extends Controller
{
    
    public function getColeccionesActivas(){
        $colecciones = DB::select("SELECT * FROM coleccion WHERE estado = 1 ORDER BY orden");
        if(count($colecciones)> 0){
            foreach ($colecciones as $key => $c) {
                $imagenes = $this->get_images_($c->id);
                $array[$key] = array('propiedades' => $c, 'imagenes' => $imagenes);
            }
        }
       
        return JsonResponse::create(array( "Mensaje"=>'Colecciones consultadas...' ,"Content"=>$array, "isOk" =>true), 200);
    }

    private function get_images_($id){
        $result = DB::select("SELECT * FROM coleccion_imagenes WHERE id_coleccion = $id ");
        return $result;
    }

    public function index() {
        $data = Coleccion::All();
        return JsonResponse::create(array("Mensaje" => "Coleccion consultadas", "Content" => $data, "isOk" => true), 200);
    }
    
    public function store(Request $request) {
        try {
            $data = $request->all();
            
            $coleccion = new Coleccion();
            $coleccion->nombre = $data['nombre'];
            $coleccion->save();
            
            return JsonResponse::create(array("Mensaje" => "Colección creada correctamente", "Content" => $data, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo crear la colección", "Content" => $exc->getMessage(), "isOk" => false, "request" => json_encode($data)), 401);
        }
    }
    
    private function extension($archivo){
        $partes = explode(".", $archivo);
        $extension = end($partes);
        return $extension;
    }
    
      public function updateOrden(Request $request) {
        try {
            $data = $request->all();
            $id = $data['id'];
            $coleccion = Coleccion::find($id);
            $coleccion->orden = $data['orden'];
            $coleccion->save();
            return JsonResponse::create(array("Mensaje" => "Orden actualizado correctamente", "Content" => $coleccion, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "Content" => json_encode($Encuesta)), 401);
        }
    }
    
    public function update(Request $request) {
        
        try {
            $data = $request->all();
            $id = $data['id'];
            $coleccion = Coleccion::find($id);
            $coleccion->nombre = $data['nombre'];
            $coleccion->save();
            
            return JsonResponse::create(array("Mensaje" => "Coleccion actualizada correctamente", "Content" => $coleccion, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo actualizar la coleccion", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
    
    public function destroy($id) {
        //
        try {
            $coleccion = Coleccion::find($id);

              $imagenes = DB::select("SELECT * FROM coleccion_imagenes WHERE id_coleccion = $id ");
            foreach ($imagenes as $key => $i) {
            
            
            
             $res = explode("public", public_path(), 2);
                $name = explode("/", $i->url, 8);
                
                
                 $file = $res[0] . "public_html/api/images/coleccion/" . $name[6];
                
                if (File::isFile($file)) {
                    File::delete($file);
                }
                
                    
                }

            $coleccion->delete();


            return JsonResponse::create(array("Mensaje" => "Colección eliminada", "Content" => $coleccion, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo eliminar la colección", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
    
    public function updateState(Request $request) {
        try {
            $data = $request->all();
            $id = $data['id'];
            $coleccion = Coleccion::find($id);
            $coleccion->estado  = $data['estado'];
            $coleccion->save();
            return JsonResponse::create(array("Mensaje" => "Colección actualizada correctamente", "Content" => $coleccion, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "Descripcion", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }

     public function getImages($id){
        $result = DB::select("SELECT * FROM coleccion_imagenes WHERE id_coleccion = $id ");
        return JsonResponse::create(array("Mensaje" => "Imagenes Consultadas", "Content" => $result, "isOk" => true), 200);
    }
    
    public function saveImage(Request $request){
        try {
            $data = $request->all();
            $extension = $this->extension($_FILES['imagen']['name']);
            if($extension !== 'jpg' && $extension !== 'png'){
                return JsonResponse::create(array('Mensaje' => "Solo se aceptan formatos .jpg y .png : formato subido: ".$extension,"Content" => $extension, "isOk" => false), 402);
            }
            $tmpfname = time().substr(md5(microtime()), 0, rand(5, 12));
            $url = URL_SERVER . "images/coleccion/".$tmpfname . ".".$extension;
            $request->file('imagen')->move("../images/coleccion", $url);
            $array[0] = array('url' => $url, 'id_coleccion' => $data['coleccion']);
            $respuesta = DB::table('coleccion_imagenes')->insert($array);
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
                
                
                 $file = $res[0] . "public_html/api/images/coleccion/" . $name[6];
                
                if (File::isFile($file)) {
                    File::delete($file);
                }
            
            DB::delete("DELETE FROM coleccion_imagenes WHERE id = $id_imagen");
            return JsonResponse::create(array('Mensaje' => "Imagen eliminada", "Content" => $data, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo eliminar la imagen", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
    
    
}