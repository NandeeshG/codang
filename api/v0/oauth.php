<?php

require_once 'error.php';
require_once 'utility.php';
require_once 'dbms.php';
require_once 'newAuthCode.php';
require_once 'curl.php';
require_once 'index.php';
ob_start();

// Do what the caller asks and update database or handle error and update database or die.
// How to return to caller?? use call_user_func_array();

function routeError($errstr, $callback=null, $paramarr=array())
{
    //call correct function as per error
    if (strcmp($errstr, 'The authorization code has expired')===0) {
        logError($errstr);
        getNewAuthCode($callback, $paramarr);
    } elseif (strcmp($errstr, "Authorization code doesn't exist or is invalid for the client")===0) {
        logError($errstr);
        getNewAuthCode($callback, $paramarr);
    } elseif (strcmp($errstr, "Invalid refresh token")===0) {
        logError($errstr);
        getNewAuthCode($callback, $paramarr);
    } elseif (strcmp($errstr, "Unauthorized for this resource scope")===0) {
        logError("VERY VAGUE ERROR - MOSTLY CONTINUE?? ".$errstr);
        refreshToken($callback, $paramarr);
    } elseif (strcmp($errstr, "New code retreived")===0) {
        logError($errstr);
        accessToken($callback, $paramarr);
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

function getAuthDetails()
{
    $authDetails = connectAndExecuteQuery('select * from apiauth');
    if ($authDetails === false) {
        logError("Auth details cannot be fetched", $authDetails);
        die();
    } else {
        $authDetails = $authDetails[0];
    }
    return $authDetails;
}

// just get new refresh token and update database
function refreshToken($callback=null, $paramarr=array())
{
    $authDetails = getAuthDetails();
    $oauth_config = array('grant_type' => 'refresh_token', 'refresh_token'=> $authDetails['refresh_token'], 'client_id' => $authDetails['client_id'],
            'client_secret' => $authDetails['client_secret']);
    $response = json_decode(curlRequest($authDetails['access_token_endpoint'], $oauth_config), true);
    $str = extractError($response);

    logInfo("Retreived request and error ".$str, $response);

    if (strcmp($str, "ok")!==0) {
        routeError($str, $callback, $paramarr);
    }

    $result = $response['result']['data'];
    $nowtime = time();
    $qr = connectAndExecuteQuery("update apiauth set gen_time='{$nowtime}', access_token='{$result['access_token']}', refresh_token='{$result['refresh_token']}', scope='{$result['scope']}'");
    //logInfo("Updated refreshToken", $result);
    if ($qr===false) {
        logError("refreshToken token not updated", $qr);
        die();
    } else {
        callBackHandler($callback, $paramarr);
    }
}

function accessToken($callback=null, $paramarr=array())
{
    $authDetails = getAuthDetails();
    $oauth_config = array('grant_type' => 'authorization_code', 'code'=> $authDetails['authorization_code'], 'client_id' => $authDetails['client_id'],'client_secret' => $authDetails['client_secret'], 'redirect_uri'=> $authDetails['redirect_uri']);

    //this curlRequest uses curl.php
    $response = json_decode(curlRequest($authDetails['access_token_endpoint'], $oauth_config), true);
    logInfo("CURL RETURNED", $response);

    $str = extractError($response);
    if (strcmp($str, "ok")!==0) {
        routeError($str, $callback, $paramarr);
    }

    $result = $response['result']['data'];
    $nowtime = time();
    $qr = connectAndExecuteQuery("update apiauth set gen_time='{$nowtime}', access_token='{$result['access_token']}', refresh_token='{$result['refresh_token']}', scope='{$result['scope']}'");
    if ($qr===false) {
        logError("access token not updated", $qr);
        die();
    } else {
        callBackHandler($callback, $paramarr);
    }
}

// get new auth code and then update database. also get new access token for that code
function getNewAuthCode($callback=null, $paramarr=array())
{
    if (isCli()===1) {
        logError("NEED NEW AUTH CODE BUT IN CLI!!");
        die();
    }
    $authDetails = getAuthDetails();
    $params = array('response_type'=>'code', 'client_id'=> $authDetails['client_id'], 'redirect_uri'=> $authDetails['redirect_uri'], 'state'=> 'xyz');
    //logInfo("1Location: ". $authDetails['authorization_code_endpoint'], $params);
    header('Location: ' . $authDetails['authorization_code_endpoint'] . '?' . http_build_query($params));
    die();
}
if (isset($_GET['code'])) {
    $qr = connectAndExecuteQuery("update apiauth set authorization_code = '{$_GET['code']}'");
    if ($qr===false) {
        logError("Cannot update AUTH");
        die();
    } else {
        $params = array('auth_rec_route'=>'New code retreived','authorization_code'=>connectAndExecuteQuery('select authorization_code from apiauth')[0]['authorization_code']);
        //header('Location: ./oauth.php'."?". http_build_query($params));
        header('Location: http://localhost:8080/'."?". http_build_query($params));
        die();
    }
}

// code 0 - get new auth code
// code 1 - get new access and refresh token
// code 2 - use refresh token to get new AT and RT
function authentication($code)
{
    $errstr="";
    $authDetails = fetchQuery('select * from apiauth', $errstr);
    if (!$authDetails) {
        die("authentication".$errstr);
    }

    if ($code === 0) {
        if (isset($_GET['code'])) {
            $qr = fetchQuery("update apiauth set authorization_code = '{$_GET['code']}'", $errstr, false);
            if ($qr===false) {
                die("code 0 isset".$errstr);
            } else {
                $_GET['code'] = null;
                authentication(1);
                return;
            }
        } else {
            $params = array('response_type'=>'code', 'client_id'=> $authDetails[0]['client_id'], 'redirect_uri'=> $authDetails[0]['redirect_uri'], 'state'=> 'xyz');
            header('Location: ' . $authDetails[0]['authorization_code_endpoint'] . '?' . http_build_query($params));
            die();
        }
    } elseif ($code===1) {
        $oauth_config = array('grant_type' => 'authorization_code', 'code'=> $authDetails[0]['authorization_code'], 'client_id' => $authDetails[0]['client_id'],'client_secret' => $authDetails[0]['client_secret'], 'redirect_uri'=> $authDetails[0]['redirect_uri']);
        $response = json_decode(curlRequest($authDetails[0]['access_token_endpoint'], $oauth_config), true);
        if (authenticationCodeError($response)) {
            authentication(0);
        }
        die(json_encode($response).newline());
        $result = $response['result']['data'];
        $nowtime = time();
        echo("code1 update data".json_encode($response).newline());
        $qr = fetchQuery("update apiauth set gen_time='{$nowtime}', access_token='{$result['access_token']}', refresh_token='{$result['refresh_token']}', scope='{$result['scope']}'", $errstr, true);
        if ($qr===false) {
            die("code1 update".$errstr);
        } else {
            echo("FRESH ACCESS TOKEN AND REFRESH TOKEN GENERATED");
        }
        //die("update apiauth set gen_time='{$nowtime}', access_token='{$result['access_token']}', refresh_token='{$result['refresh_token']}', scope='{$result['scope']}'");
    } elseif ($code===2) {
        $oauth_config = array('grant_type' => 'refresh_token', 'refresh_token'=> $authDetails[0]['refresh_token'], 'client_id' => $authDetails[0]['client_id'],
            'client_secret' => $authDetails[0]['client_secret']);
        $response = json_decode(curlRequest($authDetails[0]['access_token_endpoint'], $oauth_config), true);
        if (refreshTokenError($response) || authenticationCodeError($response)) {
            authentication(1);
            return;
        }
        //die(json_encode($response).newline());

        $result = $response['result']['data'];
        $nowtime = time();
        echo("code2 update data".json_encode($response).newline());
        $qr = fetchQuery("update apiauth set gen_time='{$nowtime}', access_token='{$result['access_token']}', refresh_token='{$result['refresh_token']}', scope='{$result['scope']}'", $errstr);
        if ($qr===false) {
            die("code2 update".$errstr);
        } else {
            echo("REFRESHED THE TOKENS");
        }
    } else {
        die("WRONG ARGUMENTS!");
    }
}
