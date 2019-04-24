<?php

function sendData($action, $body, $token = null) {

    $url = 'https://cgp-hml.safrapay.com.br/cgp/' . $action;
    $local_cert = getcwd() . '/client.pem';
    $passphrase = '';
    $json = json_encode($body);

    if (!file_exists($local_cert)) {
        echo 'Certificado nao encontrado';
        return false;
    } 
    else {

        $headers = array("Content-Type: application/json",
            "Accept-Language: pt-BR",
            "amc-aplicacao: CGP",
            "amc-session-id: a208e962-9f5c-49c9-9938-3e7f0047e36a",
            "amc-message-id: 5dc307ab-b0f9-49d0-b437-d116656898bb",
            "Content-length: " . strlen($json));
        
        if(!empty($token)){
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        
        echo 'Parametros:<br><pre>' . $json . '</pre>';
        
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, $url);
        curl_setopt($tuCurl, CURLOPT_PORT, 443);
        curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
        curl_setopt($tuCurl, CURLOPT_HEADER, 0);
        //curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3); 
        curl_setopt($tuCurl, CURLOPT_SSLCERT, $local_cert);
        //curl_setopt($tuCurl, CURLOPT_SSLKEY, ''); 
        //curl_setopt($tuCurl, CURLOPT_CAINFO, getcwd() . "/ca.pem"); 
        curl_setopt($tuCurl, CURLOPT_POST, 1); //curl_setopt($tuCurl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT, 0); 
        //curl_setopt($tuCurl, CURLOPT_TIMEOUT, 600); //timeout in seconds

        $tuData = curl_exec($tuCurl);
        if (!curl_errno($tuCurl)) {
            $info = curl_getinfo($tuCurl);
            echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'] . '<br>';
        } else {
            echo 'Curl error: ' . curl_error($tuCurl) . '<br>';
        }

        curl_close($tuCurl);

        echo 'Retorno:<br><pre>' . $tuData . '</pre>';

        $retorno = json_decode($tuData);

        return $retorno;
    }
}

$body = array('idEstrategia' => 'autenticar-cgp', 'credenciais' => new stdClass());

$token_result = sendData('authentication/authenticate', $body);

if(!empty($token_result->token)){

    $card = new stdClass();
    $card->PAN = "0000000000000000";//CARTAO
    $card->CVV2 = "000";
    $card->expiration = new stdClass();
    $card->expiration->month = "12";
    $card->expiration->year = "2022";

    $transactionDateTime = new stdClass();
    $transactionDateTime->day = "24"; 
    $transactionDateTime->month = "04";
    $transactionDateTime->hour = "13";
    $transactionDateTime->minute = "45"; 
    $transactionDateTime->second = "00";

    $merchant = new stdClass();
    $merchant->id = "000000000000000";//EC
    $merchant->name = "TEST";//GATEWAY
    $merchant->city = "SAO PAULO";
    $merchant->state = "SP";
    $merchant->country = "BR";

    $body_capture = array(
        "orderNumber"=> "12345678901234567890123456789015",//NUMERO DO PEDIDO
        "card" => $card,   
        "installments"=> 1,//PARCELAS
        "amount"=> 1000,//VALOR EM CENTAVOS
        "transactionDateTime" => $transactionDateTime,
        "captureNSU"=> "000000",//ID DA TRANSACAO
        "gatewayID"=> "00000",
        "terminalID"=> "0000000",
        "merchant" => $merchant,
        "currency"=> "986",
        );

    $payment_result = sendData('automaticcapture', $body_capture, $token_result->token);

}