<?php

require_once 'dbms.php';

function getCountry($dbconn, /*code,name*/ $argtype, $country, $pq=false)
{
    $ctry = pg_escape_literal(trim($country));
    $argg = pg_escape_identifier(trim($argtype));
    $res = nonTrnscQuery($dbconn, "select * from country where $argg=$ctry", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}
function getInstitution($dbconn, /*code,name*/ $argtype, $institution, $pq=false)
{
    $inst = pg_escape_literal(trim($institution));
    $argg = pg_escape_identifier(trim($argtype));
    $res = nonTrnscQuery($dbconn, "select * from institution where $argg=$inst", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}
function getContestByCode($dbconn, $contest, $pq=false)
{
    $cnts = pg_escape_literal(trim($contest));
    $res = nonTrnscQuery($dbconn, "select * from contest where code=$cnts", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}


//---------------------------------------------------------------------------------------
