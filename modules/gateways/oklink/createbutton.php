<?php

include '../../../dbconnect.php';
include '../../../includes/functions.php';
include '../../../includes/gatewayfunctions.php';
include '../../../includes/invoicefunctions.php';
require '/lib/Oklink.php';

$gatewaymodule = "oklink";
$GATEWAY = getGatewayVariables($gatewaymodule);

// get invoice
$invoiceId = (int) $_POST['invoiceId'];
$price     = $currency = false;
$result    = mysql_query("SELECT tblinvoices.total, tblinvoices.status, tblcurrencies.code FROM tblinvoices, tblclients, tblcurrencies where tblinvoices.userid = tblclients.id and tblclients.currency = tblcurrencies.id and tblinvoices.id=$invoiceId");
$data      = mysql_fetch_assoc($result);

if (!$data) {
    error_log('no invo found for invoice id'.$invoiceId);
    die("Invalid invoice");
}

$price    = $data['total'];
$currency = $data['code'];
$status   = $data['status'];

if ($status != 'Unpaid') {
    error_log("Invoice status must be Unpaid.  Status: ".$status);
    die('bad invoice status');
}

if ( in_array($currency,array('BTC', 'USD', 'CNY') == false) {
    error_log("{$currency} is not support,Oklink support BTC/USD/CNY only");
    die('Oklink support BTC/USD/CNY only');
}


$params = array();

$params['name']              = 'Order #'.$invoiceId,
$params['price']             = $price,
$params['price_currency']    = $currency;
$params['callback_url']      = $_POST['systemURL'].'/modules/gateways/callback/oklink.php';
$params['success_url']       = $_POST['systemURL'];

$client = Oklink::withApiKey($GATEWAY['apiKey'], $GATEWAY['apiSecret']);
$response = $client->buttonsButton($params);

if ( $response && $response->button) {
    $url = OklinkBase::WEB_BASE.'merchant/mPayOrderStemp1.do?buttonid='.$response->button->id;
    header("Location: ".$invoice['url']);
} else {
    error_log(var_dump($response));   
}
