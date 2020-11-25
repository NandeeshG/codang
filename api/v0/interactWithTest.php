<?php

require_once 'dbms.php';

//returns false on some failed query. error logged

//returns the result (use index [0] to access) or empty array (for insert) on success.
//columns - code contest done

//update returns empty array in every case

//retrieve next problem not done yet
// use sortby for consistency
function codangTestSelect($dbconn, $pq=false)
{
    return withTrnscQuery($dbconn, "select * from problem where done=0 order by problem limit 1", $pq);
}

function codangTestUpdate($dbconn, $problemcode, $pq=false)
{
    return withTrnscQuery($dbconn, "update problem set done = 1 where code='$problemcode'", $pq);
}

//add new set of problem and contest
function codangTestInsert($dbconn, $problemcode, $contestcode, $pq = false)
{
    return withTrnscQuery($dbconn, "insert into problem (code,contest,done) values ('$problemcode','$contestcode',0)", $pq);
}
