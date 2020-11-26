<?php

require_once 'curl.php';

function fetchInstitutionByName($dbconn, $institution, $pq)
{
    $institution = trim($institution);
    $API_ENDPOINT = getAuthDetails($dbconn)['api_endpoint'];
    $response = codeChefGet($dbconn, $API_ENDPOINT."institution/", array("search"=>"$institution"));
    $errcode = errorFromApi($response);
    if ($errcode===9001) {
        return $response;
    } else {
        return false;
    }
}


function fetchCountryByName($dbconn, $country, $pq)
{
    $country = trim($country);
    $API_ENDPOINT = getAuthDetails($dbconn)['api_endpoint'];
    $response = codeChefGet($dbconn, $API_ENDPOINT."country/", array("search"=>"$country"));
    $errcode = errorFromApi($response);
    if ($errcode===9001) {
        return $response;
    } else {
        return false;
    }
}

function fetchContestByCode($dbconn, $contest, $pq)
{
    $contest = trim($contest);
    $API_ENDPOINT = getAuthDetails($dbconn)['api_endpoint'];
    $response = codeChefGet($dbconn, $API_ENDPOINT."contests/".$contest, array());
    $errcode = errorFromApi($response);
    if ($errcode===9001) {
        return $response;
    } else {
        return false;
    }
}

function fetchLanguageByName($dbconn, $lang, $pq)
{
    $lang = trim($lang);
    $API_ENDPOINT = getAuthDetails($dbconn)['api_endpoint'];
    $response = codeChefGet($dbconn, $API_ENDPOINT."language/", array("search"=>$lang));
    $errcode = errorFromApi($response);
    if ($errcode===9001) {
        return $response;
    } else {
        return false;
    }
}

function fetchEndUserByName($dbconn, $username, $pq)
{
    $username = trim($username);
    $API_ENDPOINT = getAuthDetails($dbconn)['api_endpoint'];
    $response = codeChefGet($dbconn, $API_ENDPOINT."users/".$username, array());
    $errcode = errorFromApi($response);
    if ($errcode===9001) {
        return $response;
    } else {
        return false;
    }
}

function fetchSubmissionByUserNameAfterId($dbconn, $username, $afterid, $pq)
{
    $username = trim($username);
    $API_ENDPOINT = getAuthDetails($dbconn)['api_endpoint'];
    if ($afterid!==false) {
        $params = array("username"=>$username, "limit"=>20, "after"=>$afterid);
    } else {
        $params = array("username"=>$username, "limit"=>20);
    }
    $response = codeChefGet($dbconn, $API_ENDPOINT."submissions/", $params);
    $errcode = errorFromApi($response);
    if ($errcode===9001 || $errcode===9003) {
        return $response;
    } else {
        return false;
    }
}

function fetchProblemByProblemCodeAndContestCode($dbconn, $problemcode, $contestcode, $pq)
{
    $problemcode = trim($problemcode);
    $contestcode = trim($contestcode);
    $API_ENDPOINT = getAuthDetails($dbconn)['api_endpoint'];
    $response = codeChefGet($dbconn, $API_ENDPOINT."contests/$contestcode/problems/$problemcode/", array());
    $errcode = errorFromApi($response);
    if ($errcode===9001) {
        return $response;
    } else {
        return false;
    }
}

function fetchTagByNameWithOffset($dbconn, $tagname, &$offset, $pq)
{
    $tagname = trim($tagname);
    $API_ENDPOINT = getAuthDetails($dbconn)['api_endpoint'];
    $response = codeChefGet($dbconn, $API_ENDPOINT."tags/problems/", array("filter"=>$tagname, "limit"=>100, "offset"=>$offset));
    $offset += 100;
    $errcode = errorFromApi($response);
    if ($errcode===9001 || $errcode===9003) {
        return $response;
    } else {
        return false;
    }
}

//--------------------------------------------------------
//9000	ERROR_OCCURRED
//9001	SUCCESS
//9002	LIMIT_EXCEEDED
//9003	NOT_FOUND
//9004	ALREADY_EXIST
//9005	EMPTY_DATA
//9006	INVALID_DATA_RECEIVED
//9007	NOT_PERMITTED
