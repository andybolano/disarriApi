<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Banner;
use DB;
use File;
define('URL_SERVER', 'http://' . $_SERVER['HTTP_HOST'] . '/api/');
class BannerController extends Controller {


public function getBannerActivos(){
        $banners = DB::select("SELECT * FROM banner WHERE estado = 1 ");
       
        return JsonResponse::create(array( "Mensaje"=>'Banners consultados...' ,"Content"=>$banners, "isOk" =>true), 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     * GET/Banner
     */
    public function index() {
        $data =  Banner::All();
        return JsonResponse::create(array("Mensaje" => "Banners consultardos", "Content" => $data, "isOk" => true), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     * POST/Banner
     */
    public function store(Request $request) {
        try {
            $data = $request->all();
            $banner = (array) json_decode($data['banner']);
            
            $extension = $this->extension($_FILES['imagen_pc']['name']);
            if($extension !== 'jpg' && $extension !== 'png'){
                return JsonResponse::create(array('Mensaje' => "Solo se aceptan formatos .jpg y .png : formato subido: ".$extension,"Content" => $extension, "isOk" => false), 402);
            }
            
            
            $tmpfname = time().substr(md5(microtime()), 0, rand(5, 12));
            $url_pc = URL_SERVER . "images/banner/".$tmpfname . ".".$extension;
            
             $extension = $this->extension($_FILES['imagen_movil']['name']);
            if($extension !== 'jpg' && $extension !== 'png'){
                return JsonResponse::create(array('Mensaje' => "Solo se aceptan formatos .jpg y .png : formato subido: ".$extension,"Content" => $extension, "isOk" => false), 402);
            }

            $tmpfname = time().substr(md5(microtime()), 0, rand(5, 12));
            $url_movil = URL_SERVER . "images/banner/".$tmpfname . ".".$extension;
            
            
            
            $Banner = new Banner();
            $Banner->texto = $banner['texto'];
            $Banner->img_pc = $url_pc;
            $Banner->img_movil = $url_movil;
            $Banner->save();
            
             $request->file('imagen_pc')->move("../images/banner", $url_pc);
             $request->file('imagen_movil')->move("../images/banner", $url_movil);
             
            return JsonResponse::create(array("Mensaje" => "Banner guardado correctamente", "Content" => $data, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo guardar el banner", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }
    
      private function extension($archivo){
        $partes = explode(".", $archivo);
        $extension = end($partes);
        return $extension;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     * GET/Banner/{id}
     */
    public function show($id) {
        //
        try {
            $Banner = Banner::find($id);
            return JsonResponse::create(array("mensaje" => "Descripcion", "request" => json_encode($Banner), "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "request" => json_encode($id)), 401);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     * PUT/Banner/{id}
     */
    public function update(Request $request) {
        //
         try {
         $data = $request->all();
         
          $data = $request->all();
            $banner = (array) json_decode($data['banner']);
            
            
              if ($request->hasFile('imagen_pc')) {
		            $extension = $this->extension($_FILES['imagen_pc']['name']);
		            if($extension !== 'jpg' && $extension !== 'png'){
		                return JsonResponse::create(array('Mensaje' => "Solo se aceptan formatos .jpg y .png : formato subido: ".$extension,"Content" => $extension, "isOk" => false), 402);
		            }
		            
		            
		            $tmpfname = time().substr(md5(microtime()), 0, rand(5, 12));
		            $url_pc = URL_SERVER . "images/banner/".$tmpfname . ".".$extension;
            }
            
                if ($request->hasFile('imagen_movil')) {
	             $extension = $this->extension($_FILES['imagen_movil']['name']);
	            if($extension !== 'jpg' && $extension !== 'png'){
	                return JsonResponse::create(array('Mensaje' => "Solo se aceptan formatos .jpg y .png : formato subido: ".$extension,"Content" => $extension, "isOk" => false), 402);
	            }
	
	            $tmpfname = time().substr(md5(microtime()), 0, rand(5, 12));
	            $url_movil = URL_SERVER . "images/banner/".$tmpfname . ".".$extension;
            }
         $id = $banner['id'];
         $Banner = Banner::find($id);
         
         
         
         
           $Banner->texto = $banner['texto'];
           if ($request->hasFile('imagen_pc')) {
           
            $res = explode("public", public_path(), 2);
                $name = explode("/", $Banner->img_pc, 8);
                 $file = $res[0] . "public_html/api/images/banner/" . $name[6];

                if (File::isFile($file)) {
                    File::delete($file);
                }
           
            $Banner->img_pc = $url_pc;
    
            
          }
   
            if ($request->hasFile('imagen_movil')) {
             $res = explode("public", public_path(), 2);
                $name = explode("/", $Banner->img_movil, 8);
                
                 $file = $res[0] . "public_html/api/images/banner/" . $name[6];
                if (File::isFile($file)) {
                    File::delete($file);
                }
                
                
            $Banner->img_movil = $url_movil;
            
            
            
            
            }
            $Banner->save();
            
             if ($request->hasFile('imagen_pc')) {
             $request->file('imagen_pc')->move("../images/banner", $url_pc);
             }
              if ($request->hasFile('imagen_movil')) {
             $request->file('imagen_movil')->move("../images/banner", $url_movil);
             
             }
             
         return JsonResponse::create(array("Mensaje" => "Banner actualizado", "Content" => $Banner, "isOk" => true), 200);
         } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo actualizar el banner", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }

    public function destroy($id) {
        try {
             $banner = Banner::find($id);
             
             
              $res = explode("public", public_path(), 2);
                $name = explode("/", $banner->img_pc, 8);
                
                
                 $file = $res[0] . "public_html/api/images/banner/" . $name[6];
                
                if (File::isFile($file)) {
                    File::delete($file);
                }
                
                
                 $res = explode("public", public_path(), 2);
                $name = explode("/", $banner->img_movil, 8);
                
                
                 $file = $res[0] . "public_html/api/images/banner/" . $name[6];
                
                if (File::isFile($file)) {
                    File::delete($file);
                }
                
                
                
             $banner->delete();

            return JsonResponse::create(array("Mensaje" => "Banner eliminado", "Content" => $banner, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se pudo eliminar el banner", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }

    public function updateState(Request $request) {
        try {
            $data = $request->all();
            $id = $data['id'];
            $banner = Banner::find($id);
            $banner->estado  = $data['estado'];
            $banner->save();
            return JsonResponse::create(array("Mensaje" => "Banner actualizado correctamente", "Content" => $banner, "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('Mensaje' => "No se puedo actualizar el banner", "Content" => $exc->getMessage(), "isOk" => false), 401);
        }
    }

}
