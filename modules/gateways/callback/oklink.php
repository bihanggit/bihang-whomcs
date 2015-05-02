<?php


# Required File Includes
include './../../../dbconnect.php';
include './../../../includes/functions.php';
include './../../../includes/gatewayfunctions.php';
include './../../../includes/invoicefunctions.php';

require_once './../oklink/lib/Oklink.php';

$gatewaymodule = "oklink";
$GATEWAY       = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"]) {
    logTransaction($GATEWAY["name"], $_POST, 'Not activated');
    die("Oklink module not activated");
}

$client = Oklink::withApiKey($GATEWAY['apiKey'], $GATEWAY['apiSecret']);

if ( $client->checkCallback() ){
    $response = json_decode(file_get_contents('php://input'));
    # Checks invoice ID is a valid invoice number or ends processing
    $invoiceid = $response->custom;

    $transid = $response->id;
    checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does

    # Successful
    $fee = 0;
    $amount = ''; // left blank, this will auto-fill as the full balance
    if ($response->status == "completed") {
        addInvoicePayment($invoiceid, $transid, $amount, $fee, $gatewaymodule); # Apply Payment to Invoice
        logTransaction($GATEWAY["name"], $response, "The transaction is now complete.");
        break;
    }
}
