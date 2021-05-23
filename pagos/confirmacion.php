<?php


date_default_timezone_set('America/Bogota');
  
$servername = "127.0.0.1";
$username = "admin_deirisarri";
$password = "1G5_HnCzV-4{";
$dbname = "deirisarri";


$transaction_id = $_POST['transaction_id'];
$transaction_date = $_POST['transaction_date'];
$merchant_id = $_POST['merchant_id'];
$value = $_POST['value'];
$currency = $_POST['currency'];
$reference_pol = $_POST['reference_pol']; //codigo Unico
$sign = $_POST['sign'];
$state_pol = $_POST['state_pol'];
$response_message_pol = $_POST['response_message_pol'];
$reference_sale = $_POST['reference_sale'];
$test = $_POST['test'];
$payment_method_name = $_POST['payment_method_name'];


  
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$consulta = "SELECT * FROM ventas WHERE reference_sale = '".$reference_sale."'";
$result = $conn->query($consulta);

if ($result->num_rows > 0) {

while($row = $result->fetch_assoc()) {
        $id_venta = $row["id"];
    }
    
 $update = "UPDATE ventas SET reference_pol = '".$reference_pol."' , transaction_id = '".$transaction_id."', transaction_date  = '".$transaction_date ."',merchant_id = '".$merchant_id."', value = ".$value.", currency = '".$currency."', sign = '".$sign."', state_pol ='".$state_pol."',  response_message_pol = '".$response_message_pol."',reference_sale = '".$reference_sale."', test = ".$test.", payment_method_name  = '".$payment_method_name."'  WHERE id=".$id_venta."";

	if ($conn->query($update) === TRUE) {
	    echo "Record updated successfully";
	    
	   
		
	} else {
	   $conn->error;
	}
	
	

    
 }



   
$conn->close();


 
?>