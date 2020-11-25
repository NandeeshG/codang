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