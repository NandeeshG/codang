<?php

require_once 'curl.php';


function fetchCountryByName($dbconn, $country, $pq)
{
    $API_ENDPOINT = getAuthDetails($dbconn)['api_endpoint'];
    $response = codeChefGet($dbconn, $API_ENDPOINT."country/", array("search"=>"$country"));
    if (errorFromApi($response)===true) {
        return false;
    } else {
        return $response;
    }
}


//--------------------------------------------------------
/*
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
*/