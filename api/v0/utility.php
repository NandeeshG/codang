<?php

if (isCli()===0) {
    ob_start();
}

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

function extractError($response)
{
    if (strcmp($response['status'], "OK")===0||strcmp($response['status'], "ok")===0) {
        return "ok";
    } elseif ($response['result']['errors']['message']) {
        return $response['result']['errors']['message'];
    } elseif (is_array($response['result']['errors'])) {
        if ($response['result']['errors'][0]['message']) {
            return $response['result']['errors'][0]['message'];
        }
    } else {
        logError("Error not extracted!! ".json_decode($response), $response);
        return "ok";
    }
}
