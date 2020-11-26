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
            logError("No data for this country - ".$country, $data);
            return false;
        } else {
            //add to database
            $data = $data['result']['data']['content'][0];
            $ctry = pg_escape_literal(trim($country));
            $name = pg_escape_literal(trim($data['countryName']));
            $qr = nonTrnscQuery($dbconn, "insert into country (code,name) values ($ctry,$name)", $pq);
            if ($qr === false) {
                logError("Cannot insert country - ".$country, $qr);
                return false;
            }
            return "Added-".json_encode($data);
        }
    } else {
        logInfo("Already exists - ", $exists);
        return $exists;
    }
}

function addInstitutionByName($dbconn, $institution, $pq=false)
{
    $exists = getInstitution($dbconn, "name", $institution, $pq);
    if ($exists === false) {
        //fetch from api and add to dbms
        $data = fetchInstitutionByName($dbconn, $institution, $pq);
        if ($data === false) {
            logError("No data for this institution - ".$institution, $data);
            return false;
        } else {
            // add to database
            $data = $data['result']['data']['content'][0];
            $name = pg_escape_literal(trim($data['institutionName']));
            $qr = nonTrnscQuery($dbconn, "insert into institution (name) values ($name)", $pq);
            if ($qr === false) {
                logError("Cannot insert institution - ".$institution, $qr);
                return false;
            }
            return "Added-".json_encode($data);
        }
    } else {
        logInfo("Already exists - ", $exists);
        return $exists;
    }
}

// don't insert if isParent but return true
function addContestByCode($dbconn, $contest, $pq=false)
{
    $exists = getContestByCode($dbconn, $contest, $pq);
    if ($exists === false) {
        //fetch from api and add to dbms
        $data = fetchContestByCode($dbconn, $contest, $pq);
        if ($data === false) {
            logError("No data for this contest - ".$contest, $data);
            return false;
        } else {
            // add to database
            $data = $data['result']['data']['content'];
            if ($data['isParent'] === true || strcasecmp($data['isParent'], "true")===0) {
                return false;
            }
            $sd =   pg_escape_literal(strtok(trim($data['startDate']), ' '));
            $ed =   pg_escape_literal(strtok(trim($data['endDate']), ' '));
            $name = pg_escape_literal(trim($data['name']));
            $annc = pg_escape_literal(trim($data['announcements']));
            $bnr =  pg_escape_literal(trim($data['bannerFile']));
            $cnt =  pg_escape_literal(trim($contest));
            $qr = nonTrnscQuery($dbconn, "insert into contest (code,name,banner,announcement,startdate,enddate) values ($cnt,$name,$bnr,$annc,$sd,$ed)", $pq);
            if ($qr === false) {
                logError("Cannot insert contest - ".$contest, $qr);
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
