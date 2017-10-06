<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Template;

class TemplateController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     * GET/template
     */
    public function index() {
        return Template::All();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     * POST/template
     */
    public function store(Request $request) {
        try {
            $data = $request->all();
            $template = new Template();
            $template->campo = $data['campo'];
            $template->save();
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
     * GET/template/{id}
     */
    public function show($id) {
        //
        try {
            $template = Template::find($id);
            return JsonResponse::create(array("mensaje" => "Descripcion", "request" => json_encode($template), "isOk" => true), 200);
        } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "request" => json_encode($id)), 401);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     * PUT/template/{id}
     */
    public function update($id , Request $request) {
        //
         try {
         $data = $request->all();
         $template = Template::find($id);
         $template->campo  = $data['campo'];
         $template->save();
         return JsonResponse::create(array("mensaje" => "Descripcion", "request" => json_encode($template), "isOk" => true), 200);
         } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "request" => json_encode($template)), 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     * DELETE/template/{id}
     */
    public function destroy($id) {
        //
        try {
         $template = Template::find($id);
         $template->delete();
         return JsonResponse::create(array("mensaje" => "Descripcion", "request" => json_encode($template), "isOk" => true), 200);
         } catch (Exception $exc) {
            return JsonResponse::create(array('mensaje' => "Descripcion", "exception" => $exc->getMessage(), "isOk" => false, "request" => json_encode($template)), 401);
        }
    }

}
