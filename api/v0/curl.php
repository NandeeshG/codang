<?php

require_once 'oauth.php';

function curlRequest($url, $post = false, $headers = array(), $data = array(), $debug=PRINT_DEBUG)
{
    //$params = '';
    //foreach ($data as $key=>$value) {
    //    $params .= $key.'='.$value.'&';
    //}
    //$params = trim($params, '&');

    $params = http_build_query($data, "", "&", PHP_QUERY_RFC3986);

    $params = str_replace("%27", "", $params); //change 'user' to user

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

    if (curl_errno($ch)===0) {
        curl_close($ch);
        return $response;
    } else {
        curl_close($ch);
        logError('Curl error: ' . curl_error($ch), $response);
        return false;
    }
}

function codeChefGet($dbconn, $path, $data)
{
    $authDetails = getAuthDetails($dbconn);
    $headers[] = 'Authorization: Bearer ' . $authDetails['access_token'];
    $response = curlRequest($path, false, $headers, $data);
    $response = json_decode($response, true);
    $errstr = extractError($response); //for auth and rate limit exceed errors
    if (strcmp($errstr, 'ok')!==0) {
        logInfo($errstr, $response);
        if (checkWaitError($errstr)) {
            logInfo("I AM WAITING");
            sleep(5*60);
            logInfo("I AM AWAKE");
            //callback curr function
            return call_user_func_array('codeChefGet', array($dbconn, $path, $data));
        } else {
            return routeError($dbconn, $errstr, 'codeChefGet', array($dbconn, $path,$data));
        }
    } else {
        // this response may have api's errors, handle them yourself
        return $response;
    }
}
