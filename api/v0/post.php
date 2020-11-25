<?php

require_once 'dbms.php';
require_once 'fetch.php';

function addCountryByName($dbconn, $country, $pq=false)
{
    //check if already exists?
    $exists = getCountry($dbconn, "name", $country, $pq);
    if ($exists === false) {
        //if not exists, retreive from api and add to dbms
        $data = fetchCountryByName($dbconn, $country, $pq);
        if ($data === false) {
            logError("No data for country - ", $data);
            return false;
        } else {
            //add to database
            $data = $data['result']['data']['content'][0];
            $qr = nonTrnscQuery($dbconn, "insert into country (code,name) values ('{$data['countryCode']}','{$data['countryName']}')", $pq);
            if ($qr === false) {
                logError("Cannot insert country - ", $qr);
                return false;
            }
            return "Added-".json_encode($data);
        }
    } else {
        logInfo("Already exists - ", $exists);
        return $exists;
    }
}

//------------------------------------------------------------------------------------------------

/*
function addProblem($problemcode, $contestcode, $dbconn)
{
    //retreive details
    $details = retreiveProblemDetails($problemcode, $contestcode);
    //call underlying routes making sure they go through
    //finally add to database
    //return at end (if not returned false in b/w)
}

function addLanguage($languageName, $dbconn)
{
    $details = retreiveLanguageDetails($languageName);
    if ($details === false) {
        return false;
    }
    logInfo("retreved details ", $details);
    $qr = nonTransactionQuery("insert into language (name) values '($details)'", $dbconn);
    if ($qr === false) {
        logError("NOT NOT Inserted Language - ", $qr);
        return false;
    }
    logInfo("Inserted Language - ", $qr);
    return true;
}
*/
