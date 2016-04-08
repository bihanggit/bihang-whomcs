<?php

include './../../../dbconnect.php';
include './../../../includes/functions.php';
include './../../../includes/gatewayfunctions.php';
include './../../../includes/invoicefunctions.php';
require './lib/Bihang.php';

$gatewaymodule = "bihang";
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

// if ( in_array($currency,array('BTC', 'USD', 'CNY')==false ) {
//     error_log("{$currency} is not support,bihang support BTC/USD/CNY only");
//     die('bihang support BTC/USD/CNY only');
// }

$convertTo = false;
$query     = "SELECT value from tblpaymentgateways where `gateway` = '$gatewaymodule' and `setting` = 'convertto'";
$result    = mysql_query($query);
$data      = mysql_fetch_assoc($result);
if ($data) {
    $convertTo = $data['value'];
}
if ($convertTo) {
    // fetch $currency and $convertTo currencies
    $query           = "SELECT rate FROM tblcurrencies where `code` = '$currency'";
    $result          = mysql_query($query);
    $currentCurrency = mysql_fetch_assoc($result);
    if (!$currentCurrency) {
        bpLog('[ERROR] In modules/gateways/bitpay/createinvoice.php: Invalid invoice currency of ' . $currency);
        die('[ERROR] In modules/gateways/bitpay/createinvoice.php: Invalid invoice currency of ' . $currency);
    }
    $result            = mysql_query("SELECT code, rate FROM tblcurrencies where `id` = $convertTo");
    $convertToCurrency = mysql_fetch_assoc($result);
    if (!$convertToCurrency) {
        bpLog('[ERROR] In modules/gateways/bitpay/createinvoice.php: Invalid convertTo currency of ' . $convertTo);
        die('[ERROR] In modules/gateways/bitpay/createinvoice.php: Invalid convertTo currency of ' . $convertTo);
    }
    $currency = $convertToCurrency['code'];
    $price    = $price / $currentCurrency['rate'] * $convertToCurrency['rate'];
}

$params = array();

$params['name']              = 'Order #'.$invoiceId;
$params['price']             = $price;
$params['price_currency']    = $currency;
$params['callback_url']      = $_POST['systemURL'].'/modules/gateways/callback/bihang.php';
$params['success_url']       = $_POST['systemURL'];

$client = Bihang::withApiKey($GATEWAY['apiKey'], $GATEWAY['apiSecret']);
$response = $client->buttonsButton($params);

if ( $response && $response->button) {
    $url = BihangBase::WEB_BASE.'merchant/mPayOrderStemp1.do?buttonid='.$response->button->id;
    header("Location: ".$url);
} else {
    error_log(var_dump($response));
}
