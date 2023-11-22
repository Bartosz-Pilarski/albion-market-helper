<?php 
    $curlhandle = curl_init();

    function requestJSON($ch, $url) {
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            print("Curl error:".curl_error($ch));
        }
        return $response;
    }

    function JSONfromAOData(string $itemNames) {
        global $curlhandle;
        return requestJSON($curlhandle, "https://west.albion-online-data.com/api/v2/stats/prices/$itemNames.json?locations=Caerleon,Bridgewatch,Lymhurst,FortSterling,Thetford,Martlock");
    }
?>