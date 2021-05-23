<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
require_once 'lib/PayU.php';

use App\Cliente;
use App\Ventas;
use App\Producto_venta;
use App\Producto;
use DB;

/*
use PayU;
use Environment;
use PayUParameters;
use PayUPayments;
use PayUCountries;
use SupportedLanguages;

PayU::$apiKey = "880vJxy6cuoh0iY03lCC3w5ohB"; //Ingrese aquí su propio apiKey.
PayU::$apiLogin = "pRRXKOl8ikMmt9u"; //Ingrese aquí su propio apiLogin.
PayU::$merchantId = "662725"; //Ingrese aquí su Id de Comercio.
PayU::$accountId = '665298';
PayU::$language = SupportedLanguages::ES; //Seleccione el idioma.
PayU::$isTest = true; //Dejarlo True cuando sean pruebas.
//URL de Pagos
Environment::setPaymentsCustomUrl("https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi");
//URL de Consultas
Environment::setReportsCustomUrl("https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi");*/

class compraController extends Controller {


    public function pay(Request $request) {
        $data = $request->all();

        $cliente = $data['cliente'];
        $envio = $data['envio'];
        $productos = $data['productos'];
        $referenceCode = $data['referenceCode'];
        $descuento = $data['descuento'];

        $cedula = $cliente['nit'];
        $cliente_consultado = DB::table('clientes')->where('cedula', "$cedula")->first();

            if(!$cliente_consultado){
                    $cli = new Cliente();
                    $cli->nombres= $cliente['nombres']." ".$cliente['apellidos'];
                    $cli->email= $cliente['email'];
                    $cli->cedula= $cliente['nit'];
                    $cli->pais= $cliente['pais'];
                    $cli->ciudad= $cliente['ciudad'];
                    $cli->direccion= $cliente['direccion'];
                    $cli->telefono = $cliente['telefono'];
                    $cli->save();
                    $ID_CLIENTE = $cli->id;
            }else{
                $ID_CLIENTE = $cliente_consultado->id;
                $cli = Cliente::find($ID_CLIENTE);
                $cli->nombres= $cliente['nombres']." ".$cliente['apellidos'];
                $cli->email= $cliente['email'];
                $cli->pais= $cliente['pais'];
                $cli->ciudad= $cliente['ciudad'];
                $cli->direccion= $cliente['direccion'];
                $cli->telefono = $cliente['telefono'];
                $cli->save();

            }

            $apiKey = "880vJxy6cuoh0iY03lCC3w5ohB"; //Ingrese aquí su propio apiKey.
            $merchantId = "662725"; //Ingrese aquí su Id de Comercio.
            $accountId = '665298';

            $venta = new Ventas();
            $venta->idCliente = $ID_CLIENTE;
            $venta->envio = $envio;
            $venta->merchant_id = $merchantId;
            $venta->reference_sale = $referenceCode;
            $venta->descuento = $descuento;
            $venta->save();

            $ID_VENTA = $venta->id;
            $TOTAL = 0;

                foreach ($productos as $key => $p) {
                        $valor = $this->get_valor_producto($p['producto']);
                        $TOTAL += $valor*$p['cantidad'];
                        $array_productos[$key] = array('idProducto' => $p['producto'],'color'=> $p['color'],'talla' => $p['talla'],'cantidad'=> $p['cantidad'],'valor_unitario'=> $valor,'valor_total'=> $valor*$p['cantidad'],'idVenta'=>$ID_VENTA);
                }



        $respuesta = DB::table('producto_venta')->insert($array_productos);


      
    

        return JsonResponse::create(array( "result" => true, "Mensaje" => "Compra registrada", "Content" => array('apiKey' => $apiKey , 'merchantId' => $merchantId, 'accountId' => $accountId )), 200);
        

      /*  $parameters = array(
                //Ingrese aquí el identificador de la cuenta.
                PayUParameters::ACCOUNT_ID => "512321",
                //Ingrese aquí el código de referencia.
                PayUParameters::REFERENCE_CODE => "deirisarri_".$ID_VENTA,
                //Ingrese aquí la descripción.
                PayUParameters::DESCRIPTION => "payment test",

                // -- Valores --
                //Ingrese aquí el valor.        
                PayUParameters::VALUE => "$TOTAL",
                //Ingrese aquí la moneda.
                PayUParameters::CURRENCY => "COP",
                // -- pagador --
                //Ingrese aquí el nombre del pagador.
                PayUParameters::PAYER_NAME => 'APPROVED',
                //Ingrese aquí el email del pagador.
                PayUParameters::PAYER_EMAIL => $cliente['email'],
                //Ingrese aquí el teléfono de contacto del pagador.
                PayUParameters::PAYER_CONTACT_PHONE => $cliente['telefono'],
                //Ingrese aquí el documento de contacto del pagador.
                PayUParameters::PAYER_DNI => "5415668464654",
                //Ingrese aquí la dirección del pagador.
                PayUParameters::PAYER_STREET => $cliente['direccion'],
                PayUParameters::PAYER_STREET_2 => $cliente['direccion'],
                PayUParameters::PAYER_CITY => $cliente['ciudad'],
                PayUParameters::PAYER_STATE => $cliente['ciudad'],
                PayUParameters::PAYER_COUNTRY =>$cliente['pais'],
                PayUParameters::PAYER_POSTAL_CODE => "000000",
                PayUParameters::PAYER_PHONE => $cliente['telefono'],

                // -- Datos de la tarjeta de crédito -- 
                //Ingrese aquí el número de la tarjeta de crédito
                PayUParameters::CREDIT_CARD_NUMBER => "4097440000000004",
                //Ingrese aquí la fecha de vencimiento de la tarjeta de crédito
                PayUParameters::CREDIT_CARD_EXPIRATION_DATE => "2018/12",
                //Ingrese aquí el código de seguridad de la tarjeta de crédito
                PayUParameters::CREDIT_CARD_SECURITY_CODE=> "321",
                //Ingrese aquí el nombre de la tarjeta de crédito
                //VISA||MASTERCARD||AMEX||DINERS
                PayUParameters::PAYMENT_METHOD => $pago['metodo'],

                //Ingrese aquí el número de cuotas.
                PayUParameters::INSTALLMENTS_NUMBER => $pago['num_cuotas'],
                //Ingrese aquí el nombre del pais.
                PayUParameters::COUNTRY => PayUCountries::CO,
        );
            
        try {
            $payu_response = PayUPayments::doAuthorizationAndCapture($parameters);
            if ($payu_response->code == "SUCCESS") {
                if ($payu_response->transactionResponse->state == "APPROVED") {
                        $vent = Ventas::find($ID_VENTA);
                        $vent->orderId = $payu_response->transactionResponse->orderId;
                        $vent->transactionId = $payu_response->transactionResponse->transactionId;
                        $vent->trazabilityCode = $payu_response->transactionResponse->trazabilityCode;
                        $vent->authorizationCode = $payu_response->transactionResponse->authorizationCode;
                        $vent->estado_pago = $payu_response->transactionResponse->state;
                        $vent->save();
                        return JsonResponse::create(array("result" => true,"Mensaje"=>"Muchas gracias por tu compra, tu factura ha sido enviada a tu correo" , "Content" =>  $payu_response->transactionResponse->state), 200);
                } else {
                    $vent = Ventas::find($ID_VENTA);
                    $vent->orderId = $payu_response->transactionResponse->orderId;
                        $vent->transactionId = $payu_response->transactionResponse->transactionId;
                        $vent->estado_pago = $payu_response->transactionResponse->state;
                    $vent->save();
                    $response["status"] = "error";
                    $response["message"] = "NOT APPROVED";
                    return JsonResponse::create(array("result" => false, "Mensaje"=>"No se puedo realizar el pago de tu compra, Sin embargo la hemos registrado, nos pondremos en contacto contigo, muchas gracias!" , "Content" =>  $payu_response->transactionResponse->state), 200);
                }
             
             
            } else {
                //TODO
                $response["status"] = "error";
                $response["message"] = $payu_response->code;
                $statusCode = 402;
            }
        } catch (Exception $exc) {
            $response["status"] = "error";
            $response["message"] = $exc->getMessage();
            $statusCode = 500;
             return JsonResponse::create(array( "result" => false, "Mensaje" => $payu_response->transactionResponse->state, "Content" => $response), $statusCode);
        }*/
                

       
        
    }


    private function get_valor_producto($id){
        $precio = DB::table('productos')->where('id', $id)->value('precio');
        return $precio;
    }



}
