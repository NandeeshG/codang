<?php

require_once 'error.php';
require_once 'utility.php';
require_once 'dbms.php';
require_once 'interactWithTest.php';
require_once 'curl.php';
require_once 'oauth.php';

define('API_ENDPOINT', getAuthDetails()['api_endpoint']);

function retreiveProblemDetails($problemcode, $contestcode)
{
    $response = codeChefGet(API_ENDPOINT."contests/{$contestcode}/problems/{$problemcode}/", array());
    if (errorFromApi($response)===true) {
        return false;
    } else {
        return $response;
    }
}

function retreiveLanguageDetails($languageText, $exact=true)
{
    $response = codeChefGet(API_ENDPOINT."language/", array('search'=>$languageText));
    $hasError = errorFromApi($response);
    if ($hasError===true) {
        return false;
    } else {
        if ($exact===true and strcmp($response['result']['data']['content'][0]['shortName'], $languageText)===0) {
            return $response['result']['data']['content'][0]['shortName'];
        } elseif ($exact===false) {
            return $response['result']['data']['content'][0]['shortName'];
        } else {
            return false;
        }
    }
}
