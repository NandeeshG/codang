<?php

require_once 'error.php';
require_once 'utility.php';
require_once 'dbms.php';

define('CD', "codang_test");

//columns - code contest done

//retrieve next problem not done yet
// use sortby for consistency
function getNextProblem()
{
    return connectAndExecuteQuery('select * from problem where done=0 order by problem limit 1', CD);
}

function markProblemDone($problemcode)
{
    return connectAndExecuteQuery("update problem set done = 1 where code='$problemcode'", CD);
}

//add new set of problem and contest
function addNewProblem($problemcode, $contestcode)
{
    return connectAndExecuteQuery("insert into problem (code,contest,done) values ('$problemcode','$contestcode',0)", CD);
}

//returns false on some failed query. error logged
//returns the result (use index [0] to access) or empty array (for insert) on success.
//update returns empty array in every case
