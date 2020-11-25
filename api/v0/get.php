<?php

require_once 'dbms.php';

function getCountry($dbconn, $argtype, $country, $pq=false)
{
    if (strcasecmp($argtype, "code")===0) {
        $res = nonTrnscQuery($dbconn, "select * from country where code='$country'", $pq);
    } else {
        $res = nonTrnscQuery($dbconn, "select * from country where name='$country'", $pq);
    }
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}
