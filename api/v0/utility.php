<?php

if (isCli()===0) {
    ob_start();
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
define('DEV', 123456);
define('PROD', 654321);
define('MY_ENV', DEV);
define('PRINT_QUERY', false); //dbms.php
define('PRINT_DEBUG', true); //curl.php
define('PQ', false); //oauth.php
define('EXIT_ON_ERROR', false);

error_reporting(E_ALL); // Error/Exception engine, always use E_ALL
ini_set('ignore_repeated_errors', true); // always use TRUE
ini_set('display_errors', false); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment
ini_set('log_errors', true); // Error/Exception file logging engine.
//error go to codang.error.log
//ini_set('error_log', '/home/nandeesh/Documents/learn/codang/api/v0/php_error.log'); // Logging file path
//error_log("Hello, errors!");

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
    $message = "ok";
    //below two for auth related and rate limited
    if ($response['result']['errors']['message']) {
        if ($response['result']['errors']['code']<9000) {
            $message = $response['result']['errors']['message'];
        }
    } elseif (is_array($response['result']['errors'])) {
        if ($response['result']['errors'][0]['message']) {
            if ($response['result']['errors'][0]['code']<9000) {
                $message = $response['result']['errors'][0]['message'];
            }
        }
    }
    if (strcmp($message, "you are not permitted to access this information")===0) {
        return "ok";
    } else {
        return $message;
    }
}

//handles only api errors
//return true if error found else return code
function errorFromApi($response, $lastcode_different=false)
{
    if ($response === false) {
        logError("empty response!");
        return true;
    }
    $code = $response['result']['data']['code'];
    if (is_integer($code)===false) {
        $code = $response['result']['errors']['code'];
    }
    if (is_integer($code)===false) {
        $code = $response['result']['errors'][0]['code'];
    }
    if (is_integer($code)===false) {
        $code = $response['result'][0]['errors'][0]['code'];
    }

    logInfo("code - ".$code, $response);
    if ($lastcode_different===true and $code===9007) {
        return $code;
    } elseif ($code===9000 || $code===9002 || ($code>=9005 and $code===9007)) {
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
