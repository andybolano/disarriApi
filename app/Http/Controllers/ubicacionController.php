<?php
namespace App\Http\Controllers;
use DB;


class ubicacionController extends Controller {

    public function getPaises() {
        $paises = DB::select(DB::raw("SELECT * FROM paises ORDER BY name ASC"));
     
        return $paises;
    }

    public function getCiudades($pais) {
        $departamentos = DB::select(DB::raw("SELECT * FROM states WHERE country_id = '$pais' ORDER BY name ASC "));
        return $departamentos;
    }
    
      public function getDepartament($city) {
        $departamentos = DB::select(DB::raw("SELECT * FROM cities WHERE state_id = '$city' ORDER BY name ASC "));
        return $departamentos;
    }

   
    
}
