<?php

include 'include/conn.php';
include 'include/session.php';

// Get order number from URL parameter
$order_number = isset($_GET['order_number']) ? mysqli_real_escape_string($conn, $_GET['order_number']) : '';

if (empty($order_number)) {
    die("Error: Order number is required!");
}

$query = mysqli_query($conn, "SELECT * FROM customer_order_summary WHERE order_number = '$order_number'");

if (!$query || mysqli_num_rows($query) == 0) {
    die("Error: Order not found!");
}

$result = mysqli_fetch_assoc($query);
$RM = $result['total_amount'] * 100;
$ref = $result['order_number'];

  $some_data = array(
    'userSecretKey'=>'c3alqk05-mwhy-aib7-fya0-1lg7zaquxd57',
    'categoryCode'=>'5daw6bza',
    'billName'=>'Your Order',
    'billDescription'=>'Your Order Description',
    'billPriceSetting'=>1,
    'billPayorInfo'=>0,
    'billAmount'=>$RM,
    'billReturnUrl'=>'http://bizapp.my',
    'billCallbackUrl'=>'http://bizapp.my/paystatus',
    'billExternalReferenceNo' => $ref,
    'billTo'=>'',
    'billEmail'=>'',
    'billPhone'=>'',
    'billSplitPayment'=>0,
    'billSplitPaymentArgs'=>'',
    'billPaymentChannel'=>'0',
    'billContentEmail'=>'Thank you for purchasing our product!',
    'billChargeToCustomer'=>1,
    'billExpiryDate'=>'',
    'billExpiryDays'=>1
  );  

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_URL, 'https://dev.toyyibpay.com/index.php/api/createBill');  
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);

  $result = curl_exec($curl);
  $info = curl_getinfo($curl);  
  curl_close($curl);
  $obj = json_decode($result);
  
  // Check if bill was created successfully
  if(isset($obj[0]->BillCode)) {
    $billCode = $obj[0]->BillCode;
    header("Location: https://dev.toyyibpay.com/" . $billCode);
    exit();
  } else {
    echo "Error creating bill: " . $result;
  }