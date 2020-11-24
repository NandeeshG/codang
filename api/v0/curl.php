<?php

require_once 'error.php';
require_once 'utility.php';
require_once 'oauth.php';
require_once 'index.php';

function curlRequest($url, $post = false, $headers = array(), $data = array(), $debug=false)
{
    //$params = '';
    //foreach ($data as $key=>$value) {
    //    $params .= $key.'='.$value.'&';
    //}
    //$params = trim($params, '&');

    $params = http_build_query($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url.'?'.$params); //Url together with parameters
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //don't return instead of printing

    if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
    }

    $headers[] = 'content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    if ($debug) {
        logInfo("CURL URL - ".curl_getinfo($ch)['url'].newline(), $response);
    }

    if (curl_errno($ch)) {  //catch if curl error exists and show it
        curl_close($ch);
        logError('Curl error: ' . curl_error($ch), $response);
    } else {
        curl_close($ch);
        return $response;
    }
}

function codeChefGet($path, $data)
{
    $authDetails = getAuthDetails();
    $headers[] = 'Authorization: Bearer ' . $authDetails['access_token'];
    $response = curlRequest($path, false, $headers, $data);
    $response = json_decode($response, true);
    $errstr = extractError($response);
    if (strcmp($errstr, 'ok')!==0) {
        logError($errstr, $response);
        if (checkWaitError($errstr)) {
            logInfo("I AM WAITING");
            sleep(5*60);
            logInfo("I AM AWAKE");
        } else {
            routeError($errstr, 'codeChefGet', array($path,$data));
        }
        return false;
    } else {
        return $response;
    }
}
