<?php

require_once 'dbms.php';

//returns false on some failed query. error logged

//returns the result (use index [0] to access) or empty array (for insert) on success.
//columns - code contest done

//update returns empty array in every case

//retrieve next problem not done yet
// use sortby for consistency
function codangTestNextProblem($dbconn, $pq=false)
{
    return withTrnscQuery($dbconn, "select * from problem where done=0 order by problem limit 1", $pq);
}

function codangTestMarkProblem($dbconn, $problemcode, $pq=false)
{
    $arg = pg_escape_literal($dbconn,trim($problemcode));
    return withTrnscQuery($dbconn, "update problem set done = 1 where code=$arg", $pq);
}

//add new set of problem and contest
function codangTestAddProblem($dbconn, $problemcode, $contestcode, $pq = false)
{
    $arg1 = pg_escape_literal($dbconn,trim($problemcode));
    $arg2 = pg_escape_literal($dbconn,trim($contestcode));
    if (strcasecmp($arg1, "")===0 or strcasecmp($arg2, "")===0) {
        logInfo("Fetched submission single ", array($arg1, $arg2));
        die();
    }
 
    $exists = nonTrnscQuery($dbconn, "select * from problem where code=$arg1", $pq);
    if ($exists === false) {
        return withTrnscQuery($dbconn, "insert into problem (code,contest,done) values ($arg1,$arg2,0)", $pq);
    } else {
        return $exists;
    }
}

//---------------------------------------------------------
//retrieve next problem not done yet
// use sortby for consistency
function codangTestNextContest($dbconn, $pq=false)
{
    return withTrnscQuery($dbconn, "select * from contest where done=0 order by contest limit 1", $pq);
}

function codangTestMarkContest($dbconn, $contestcode, $pq=false)
{
    $arg2 = pg_escape_literal($dbconn,trim($contestcode));
    return withTrnscQuery($dbconn, "update contest set done = 1 where code=$arg2", $pq);
}

//add new set of problem and contest
//BEWARE THIS FUNCTION DOESN'T CHECK IF CONTEST ALREADY EXISTS, THUS THROWING AN ERROR
function codangTestAddContest($dbconn, $contestcode, $pq = false)
{
    $arg2 = pg_escape_literal($dbconn,trim($contestcode));
    return withTrnscQuery($dbconn, "insert into contest (code,done) values ($arg2,0)", $pq);
}
