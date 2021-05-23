<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Blog;
use DB;
use File;
define('URL_SERVER', 'http://' . $_SERVER['HTTP_HOST'] . '/api/');

class blogController extends Controller
{
    public function getBlogActivos(){
        $blogs = DB::select("SELECT * FROM blog WHERE estado = 1 ORDER BY fecha DESC");
       
        return JsonResponse::create(array( "Mensaje"=>'Blogs consultados...' ,"Content"=>$blogs, "isOk" =>true), 200);
    }
    public function index() {
        $data = DB::select("SELECT * FROM blog  ORDER BY fecha DESC");
        return JsonResponse::create(array("Mensaje" => "Blogs consultados", "Content" => $data, "isOk" => true), 200);
    }
    
    public function store(Request $request) {
        try {
            $data = $request->all();
            
            $articulo = (array) json_decode($data['articulo']);
            
            $extension = $this->extension($_FILES['imagen']['name']);
            if($extension !== 'jpg' && $extension !== 'png'){
                return JsonResponse::create(array('Mensaje' => "Solo se aceptan formatos .jpg y .png : formato subido: ".$extension,"Content" => $extension, "isOk" => false), 402);
            }
            
            $tmpfname = time().substr(md5(microtime()), 0, rand(5, 12));
            $url = URL_SERVER . "images/blog/".$tmpfname . ".".$extension;
            
            $blog = new Blog();
            $blog->titulo = $articulo['titulo'];
            $blog->descripcion = $articulo['descripcion'];
            $blog->url = $articulo['url'];
            $blog->imagen = $url;
            $blog->fecha = $articulo['fecha'];
            $blog->save();
            
            $request->file('imagen')->move("../images/blog", $url);
            
            return JsonResponse::create(array("Mensaje" => "Descripcion", "Content" => json_encode($data), "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "Descripcion", "Content" => $exc->getMessage(), "isOk" => false, "request" => json_encode($data)), 401);
        }
    }
    
    private function extension($archivo){
        $partes = explode(".", $archivo);
        $extension = end($partes);
        return $extension;
    }
    
    public function show($id) {
        //
        try {
            $template = Template::find($id);
            return JsonResponse::create(array("mensaje" => "Descripcion", "request" => json_encode($template), "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "request" => json_encode($id)), 401);
        }
    }
    
    public function update(Request $request) {
  
        try {
            $data = $request->all();
            
            $articulo = (array) json_decode($data['articulo']);
            $blog = Blog::find($articulo['id']);
            
           
             
            if ($request->hasFile('imagen')) {
            
          
                $extension = $this->extension($_FILES['imagen']['name']);
                $tmpfname = time().substr(md5(microtime()), 0, rand(5, 12));
                $url = URL_SERVER . "images/blog/".$tmpfname . ".".$extension;
            }
            
            
            $blog->titulo = $articulo['titulo'];
            $blog->descripcion = $articulo['descripcion'];
            $blog->url = $articulo['url'];
            $blog->fecha = $articulo['fecha'];
            if ($request->hasFile('imagen')) {
                $blog->imagen = $url;
            }
            $blog->save();
            
        
            
            if ($request->hasFile('imagen')) {
                
              
                 $res = explode("public", public_path(), 2);
                $name = explode("/", $articulo['imagen'], 8);
                 $file = $res[0] . "images\blog" . chr(92) . $name[6];
                 
                if (File::isFile($file)) {
                    File::delete($file);
                }

               
                $request->file('imagen')->move("../images/blog", $url);
            }
            
            return JsonResponse::create(array("Mensaje" => "Artículo actualizado correctamente", "Content" => $blog, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo actualizar el artículo", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
   
    public function destroy($id) {
        //
        try {
             $blog = Blog::find($id);

                  $res = explode("public", public_path(), 2);
                $name = explode("/", $blog->imagen, 8);
                
                 $file = $res[0] . "images\blog" . chr(92) . $name[6];
                if (File::isFile($file)) {
                    File::delete($file);
                }
                

            $blog->delete();


            return JsonResponse::create(array("Mensaje" => "Artículo eliminado", "Content" => $blog, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo eliminar el artículo", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }

    public function updateState(Request $request) {
        try {
            $data = $request->all();
            $id = $data['id'];
            $blog = Blog::find($id);
            $blog->estado  = $data['estado'];
            $blog->save();
            return JsonResponse::create(array("Mensaje" => "Articulo actualizado correctamente", "Content" => $blog, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "Descripcion", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
    
    
}