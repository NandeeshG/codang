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