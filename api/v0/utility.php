<?php

if (isCli()===0) {
    ob_start();
}
define('DEV', 123456);
define('PROD', 654321);
define('MY_ENV', DEV);
define("PRINT_QUERY", false); //dbms.php
define('PRINT_DEBUG', false); //curl.php
define('PQ', false); //oauth.php
definr('EXIT_ON_ERROR',true);

function isCli()
{
    if (strcmp(php_sapi_name(), 'cli')===0) {
        return 1;
    } else {
        return 0;
    }
}

function newline($str="")
{
    $endl = isCli()===1 ? "\n" : "<br/>";
    return $str . $endl;
}

function checkWaitError($str)
{
    if (strcmp($str, 'API request limit exhausted')===0) {
        return true;
    } else {
        return false;
    }
}

//handles only wait and auth errors
function extractError($response)
{
    if (strcasecmp($response['status'], "ok")===0) {
        return "ok";
    }

    //below two for auth related and rate limited
    if ($response['result']['errors']['message']) {
        return $response['result']['errors']['message'];
    } elseif (is_array($response['result']['errors'])) {
        if ($response['result']['errors'][0]['message']) {
            return $response['result']['errors'][0]['message'];
        }
    }

    return "ok";
}

//handles only api errors
//return true if error found else return code
function errorFromApi($response)
{
    if ($response === false) {
        logError("empty response!");
        return true;
    }
    $code = $response['result']['data']['code'];
    logInfo("code - ".$code, $response);
    if ($code===9000 || $code===9002 || ($code>=9005 and $code===9007)) {
        return true;
    } else { //9001 or 9003 or 9004 handle on own
        return $code;
    }
    //these will decide if we want to rollback transaction or not, thus be careful with them
}
//9000	ERROR_OCCURRED
//9001	SUCCESS
//9002	LIMIT_EXCEEDED
//9003	NOT_FOUND
//9004	ALREADY_EXIST
//9005	EMPTY_DATA
//9006	INVALID_DATA_RECEIVED
//9007	NOT_PERMITTED
