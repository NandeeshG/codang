<?php

require_once 'error.php';
require_once 'utility.php';
require_once 'dbms.php';
require_once 'interactWithTest.php';
require_once 'curl.php';
require_once 'oauth.php';

define('API_ENDPOINT', getAuthDetails()['api_endpoint']);

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
