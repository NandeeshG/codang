<?php

require_once 'dbms.php';

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
function getLanguageByName($dbconn, $lang, $pq=false)
{
    $arg = pg_escape_literal(trim($lang));
    $res = nonTrnscQuery($dbconn, "select * from language where name=$arg", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}

function getEndUserByName($dbconn, $username, $pq=false)
{
    $arg = pg_escape_literal(trim($username));
    $res = nonTrnscQuery($dbconn, "select * from enduser where username=$arg", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return true;
    }
}

function getSubmissionByName($dbconn, $username, $pq, $option)
{
    $arg = pg_escape_literal(trim($username));
    if (strcasecmp($option, "oldest")===0) {
        $res = nonTrnscQuery($dbconn, "select id from submission order by id where username=$arg limit 1", $pq);
        if ($res === false or count($res)===0) {
            return false;
        } else {
            return $res[0]['id'];
        }
    } else {
        $res = nonTrnscQuery($dbconn, "select * from submission order by id where username=$arg", $pq);
        if ($res === false or count($res)===0) {
            return false;
        } else {
            return $res;
        }
    }
}

function getProblemByCode($dbconn, $problemcode, $pq)
{
    $arg = pg_escape_literal(trim($problemcode));
    $res = nonTrnscQuery($dbconn, "select * from problem where code=$arg", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}

function getProblemLanguageByCodes($dbconn, $problemcode, $languagecode, $pq)
{
    $arg = pg_escape_literal(trim($problemcode));
    $arg2 = pg_escape_literal(trim($languagecode));
    $res = nonTrnscQuery($dbconn, "select * from problemlanguage where problemcode=$arg and languagecode=$languagecode", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}

function getTagByNameByOwner($dbconn, $tag, $owner, $pq)
{
    $arg = pg_escape_literal(trim($tag));
    $arg2 = pg_escape_literal(trim($owner));
    $res = nonTrnscQuery($dbconn, "select * from tag where name=$arg and owner=$arg2", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}

function getCategoryByName($dbconn, $category, $pq)
{
    $arg = pg_escape_literal(trim($category));
    $res = nonTrnscQuery($dbconn, "select * from category where name=$arg", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}


//---------------------------------------------------------------------------------------
