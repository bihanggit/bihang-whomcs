<?php

/**
 * @return array
 */
function bihang_config()
{
    $configarray = array(
        "FriendlyName" => array(
            "Type" => "System",
            "Value"=> "Bihang"
        ),
        'apiKey' => array(
            'FriendlyName' => 'API Key from your bihang.com account.',
            'Type'         => 'text'
        ),
        'apiSecret' => array(
            'FriendlyName' => 'API Secret from your bihang.com account.',
            'Type'         => 'text'
        ),
    );

    return $configarray;
}

/**
 * @param array $params
 *
 * @return string
 */
function bihang_link($params)
{
    # Invoice Variables
    $invoiceid = $params['invoiceid'];

    # Client Variables
    $firstname = $params['clientdetails']['firstname'];
    $lastname  = $params['clientdetails']['lastname'];
    $email     = $params['clientdetails']['email'];
    $address1  = $params['clientdetails']['address1'];
    $address2  = $params['clientdetails']['address2'];
    $city      = $params['clientdetails']['city'];
    $state     = $params['clientdetails']['state'];
    $postcode  = $params['clientdetails']['postcode'];
    $country   = $params['clientdetails']['country'];
    $phone     = $params['clientdetails']['phonenumber'];

    # System Variables

    $systemurl = $params['systemurl'];

    $post = array(
        'invoiceId'     => $invoiceid,
        'systemURL'     => $systemurl,
        'buyerName'     => "$firstname $lastname",
        'buyerAddress1' => $address1,
        'buyerAddress2' => $address2,
        'buyerCity'     => $city,
        'buyerState'    => $state,
        'buyerZip'      => $postcode,
        'buyerEmail'    => $email,
        'buyerPhone'    => $phone,
    );

    $form = '<form action="'.$systemurl.'/modules/gateways/bihang/createbutton.php" method="POST">';

    foreach ($post as $key => $value) {
        $form.= '<input type="hidden" name="'.$key.'" value = "'.$value.'" />';
    }

    $form.='<input type="submit" value="'.$params['langpaynow'].'" />';
    $form.='</form>';

    return $form;
}
