<?php

require_once 'curl.php';

function fetchCountryByName($dbconn, $country, $pq)
{
    $API_ENDPOINT = getAuthDetails($dbconn)['api_endpoint'];
    $response = codeChefGet($dbconn, $API_ENDPOINT."country/", array("search"=>"$country"));
    $errcode = errorFromApi($response);
    if ($errcode===9001) {
        return $response;
    } else {
        return false;
    }
}
function fetchInstitutionByName($dbconn, $institution, $pq)
{
    $API_ENDPOINT = getAuthDetails($dbconn)['api_endpoint'];
    $response = codeChefGet($dbconn, $API_ENDPOINT."institution/", array("search"=>"$institution"));
    $errcode = errorFromApi($response);
    if ($errcode===9001) {
        return $response;
    } else {
        return false;
    }
}
function fetchContestByCode($dbconn, $contest, $pq)
{
    $API_ENDPOINT = getAuthDetails($dbconn)['api_endpoint'];
    $response = codeChefGet($dbconn, $API_ENDPOINT."contests/".$contest, array());
    $errcode = errorFromApi($response);
    if ($errcode===9001) {
        return $response;
    } else {
        return false;
    }
}


//--------------------------------------------------------
