<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

require_once 'lib/PayU.php';

PayU::$apiKey = "4JM8M4HGFBEZZRRuubA6aKrcU0"; //Ingrese aquí su propio apiKey.
PayU::$apiLogin = "OBM6sJr3nxv569n"; //Ingrese aquí su propio apiLogin.
PayU::$merchantId = "682971"; //Ingrese aquí su Id de Comercio.
PayU::$language = SupportedLanguages::ES; //Seleccione el idioma.
PayU::$isTest = true; //Dejarlo True cuando sean pruebas.

//URL de Pagos
Environment::setPaymentsCustomUrl("https://stg.api.payulatam.com/payments-api/4.0/service.cgi");
//URL de Consultas
Environment::setReportsCustomUrl("https://stg.api.payulatam.com/reports-api/4.0/service.cgi");

class compraController extends Controller {


    public function pay() {
        echo "bien";
    }

}
