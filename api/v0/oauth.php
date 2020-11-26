<?php

require_once 'error.php';

require_once 'curl.php'; //to allow callbacks

// How to return to caller?? use call_user_func_array();

function routeError($dbconn, $errstr, $callback=null, $paramarr=array())
{
    //call correct function as per error
    if (strcmp($errstr, 'The authorization code has expired')===0) {
        logError($errstr);
        getNewAuthCode($dbconn, $callback, $paramarr);
    } elseif (strcmp($errstr, "Authorization code doesn't exist or is invalid for the client")===0) {
        logError($errstr);
        getNewAuthCode($dbconn, $callback, $paramarr);
    } elseif (strcmp($errstr, "Invalid refresh token")===0) {
        logError($errstr);
        getNewAuthCode($dbconn, $callback, $paramarr);
    } elseif (strcmp($errstr, "Unauthorized for this resource scope")===0) {
        logError("VERY VAGUE ERROR - MOSTLY CONTINUE?? ".$errstr);
        refreshToken($dbconn, $callback, $paramarr);
    } elseif (strcmp($errstr, "New code retreived")===0) {
        logError($errstr);
        accessToken($dbconn, $callback, $paramarr);
    } else {
        logError("NEW ERROR FOUND - ".$errstr);
        die();
    }
}

//do callback or goto localhost:8080
function callBackHandler($callback=null, $paramarr=array())
{
    if ($callback===null) {
        header('Location: http://localhost:8080/');
    } else {
        call_user_func_array($callback, $paramarr);
    }
    die();
}

function getAuthDetails($dbconn)
{
    $authDetails = nonTrnscQuery($dbconn, "select * from apiauth", PQ);
    if ($authDetails === false) {
        logError("Auth details cannot be fetched", $authDetails);
        die();
    } else {
        $authDetails = $authDetails[0];
    }
    return $authDetails;
}

// just get new refresh token and update database
function refreshToken($dbconn, $callback=null, $paramarr=array())
{
    $authDetails = getAuthDetails($dbconn);
    $oauth_config = array('grant_type' => 'refresh_token', 'refresh_token'=> $authDetails['refresh_token'], 'client_id' => $authDetails['client_id'],
            'client_secret' => $authDetails['client_secret']);
    $response = json_decode(curlRequest($authDetails['access_token_endpoint'], $oauth_config), true);
    $str = extractError($response);

    logInfo("Retreived request and error ".$str, $response);

    if (strcmp($str, "ok")!==0) {
        routeError($dbconn, $str, $callback, $paramarr);
    }

    $result = $response['result']['data'];
    $nowtime = time();
    $qr = nonTrnscQuery($dbconn, "update apiauth set gen_time='{$nowtime}', access_token='{$result['access_token']}', refresh_token='{$result['refresh_token']}', scope='{$result['scope']}'", PQ);
    //logInfo("Updated refreshToken", $result);
    if ($qr===false) {
        logError("refreshToken token not updated", $qr);
        die();
    } else {
        callBackHandler($callback, $paramarr);
    }
}

function accessToken($dbconn, $callback=null, $paramarr=array())
{
    $authDetails = getAuthDetails($dbconn);
    $oauth_config = array('grant_type' => 'authorization_code', 'code'=> $authDetails['authorization_code'], 'client_id' => $authDetails['client_id'],'client_secret' => $authDetails['client_secret'], 'redirect_uri'=> $authDetails['redirect_uri']);

    //this curlRequest uses curl.php
    $response = json_decode(curlRequest($authDetails['access_token_endpoint'], $oauth_config), true);
    logInfo("CURL RETURNED", $response);

    $str = extractError($response);
    if (strcmp($str, "ok")!==0) {
        routeError($dbconn, $str, $callback, $paramarr);
    }

    $result = $response['result']['data'];
    $nowtime = time();
    $qr = nonTrnscQuery($dbconn, "update apiauth set gen_time='{$nowtime}', access_token='{$result['access_token']}', refresh_token='{$result['refresh_token']}', scope='{$result['scope']}'", PQ);
    if ($qr===false) {
        logError("access token not updated", $qr);
        die();
    } else {
        callBackHandler($callback, $paramarr);
    }
}

// get new auth code and then update database. also get new access token for that code
function getNewAuthCode($dbconn, $callback=null, $paramarr=array())
{
    if (isCli()===1) {
        logError("NEED NEW AUTH CODE BUT IN CLI!!");
        die();
    }
    $authDetails = getAuthDetails($dbconn);
    $params = array('response_type'=>'code', 'client_id'=> $authDetails['client_id'], 'redirect_uri'=> $authDetails['redirect_uri'], 'state'=> 'xyz');
    //logInfo("1Location: ". $authDetails['authorization_code_endpoint'], $params);
    header('Location: ' . $authDetails['authorization_code_endpoint'] . '?' . http_build_query($params));
    exit();
    die();
}

if (isset($_GET['code'])) {
    $temp = handleConnect("codang", "open", PQ);
    $qr = withTrnscQuery($temp, "update apiauth set authorization_code = '{$_GET['code']}'", PQ);
    if ($qr===false) {
        logError("Cannot update AUTH");
        handleConnect($temp, "close", PQ);
        die();
    } else {
        $params = array('auth_rec_route'=>'New code retreived','authorization_code'=> getAuthDetails($temp)['authorization_code']);
        handleConnect($temp, "close", PQ);
        //header('Location: ./oauth.php'."?". http_build_query($params));
        header('Location: http://localhost:8080/'."?". http_build_query($params));
        die();
    }
}
