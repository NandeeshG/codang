<?php

require_once 'dbms.php';

function getInstitution($dbconn, /*code,name*/ $argtype, $institution, $pq=false)
{
    $inst = pg_escape_literal($dbconn, trim($institution));
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
    $ctry = pg_escape_literal($dbconn, trim($country));
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
    $cnts = pg_escape_literal($dbconn, trim($contest));
    $res = nonTrnscQuery($dbconn, "select * from contest where code=$cnts", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}
function getLanguageByName($dbconn, $lang, $pq=false)
{
    $arg = pg_escape_literal($dbconn, trim($lang));
    $res = nonTrnscQuery($dbconn, "select * from language where name=$arg", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}

function getEndUserByName($dbconn, $username, $pq=false)
{
    $arg = pg_escape_literal($dbconn, trim($username));
    $res = nonTrnscQuery($dbconn, "select * from enduser where username=$arg", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return true;
    }
}

function getSubmissionByName($dbconn, $username, $pq, $option)
{
    $arg = pg_escape_literal($dbconn, trim($username));
    if (strcasecmp($option, "oldest")===0) {
        $res = nonTrnscQuery($dbconn, "select id from submission where username=$arg order by id limit 1", $pq);
        if ($res === false or count($res)===0) {
            return false;
        } else {
            return $res[0]['id'];
        }
    } else {
        $res = nonTrnscQuery($dbconn, "select * from submission where username=$arg order by id ", $pq);
        if ($res === false or count($res)===0) {
            return false;
        } else {
            return $res;
        }
    }
}

function getProblemByCode($dbconn, $problemcode, $pq)
{
    $arg = pg_escape_literal($dbconn, trim($problemcode));
    $res = nonTrnscQuery($dbconn, "select * from problem where code=$arg", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}

function getProblemLanguageByCodes($dbconn, $problemcode, $languagecode, $pq)
{
    $arg = pg_escape_literal($dbconn, trim($problemcode));
    $arg2 = pg_escape_literal($dbconn, trim($languagecode));
    $res = nonTrnscQuery($dbconn, "select * from problemlanguage where problemcode=$arg and languagecode=$arg2", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}

function getTagByNameByOwner($dbconn, $tag, $owner, $pq)
{
    $arg = pg_escape_literal($dbconn, trim($tag));
    $arg2 = pg_escape_literal($dbconn, trim($owner));
    $res = nonTrnscQuery($dbconn, "select * from tag where name=$arg and owner=$arg2", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}

function getCategoryByName($dbconn, $category, $pq)
{
    $arg = pg_escape_literal($dbconn, trim($category));
    $res = nonTrnscQuery($dbconn, "select * from category where name=$arg", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}

function getCategories($dbconn, $pq)
{
    $res = nonTrnscQuery($dbconn, "select name from category", $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}

function getTagsByCategory($dbconn, $category, $pq)
{
    $category = trim($category);
    if (strcmp($category, "")===0 or $category==false) {
        return false;
    }
    if (strcasecmp($category, "all")===0) {
        $res = nonTrnscQuery($dbconn, "select code as tagcode, name as tagname from tag", $pq);
    } else {
        $category = pg_escape_literal($category);
        $res = nonTrnscQuery($dbconn, "select tagcode,tagname from select_tag_by_category_func($category)", $pq);
    }
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}

//public only
function getProblemsByTagListOR($dbconn, $taglist, $pq)
{
    foreach ($taglist as $tag) {
        $tag = trim($tag);
        if (strcmp($tag, "")===0 or $tag==false) {
            return false;
        }
        //$tag = pg_escape_literal($tag);
        $res = nonTrnscQuery($dbconn, "select code,name,date,contestcode,author from select_problem_by_tag_owner_func('{$tag}','public')", $pq);
        if ($res === false or count($res)===0) {
            return false;
        } else {
            foreach ($res as $prob) {
                //$ret[] = json_encode($prob);
                $ret[] = $prob;
            }
            //$ret = array_unique($ret);
        }
    }
    if ($ret===false or count($ret)===0) {
        return false;
    } else {
        return $ret;
    }
}

//public only
function getProblemsByTagListAND($dbconn, $taglist, $pq)
{
    if (count($taglist) === 0) {
        return false;
    }
    $query = "select problem.* from problem where problem.code in (select problemtag.problemcode from problemtag join tag on tag.code=problemtag.tagcode where tag.name in (";
    $append = "";
    foreach ($taglist as $tag) {
        $tag = pg_escape_literal($tag);
        $append .= $tag;
        $append .= ",";
    }
    $append = trim($append, ",");
    $query .= $append;
    $query .= ") group by problemtag.problemcode having count(distinct tag.name)=";
    $query .= (int)(count($taglist));
    $query .= ")";
    //return $query;

    $res = nonTrnscQuery($dbconn, $query, $pq);
    if ($res === false or count($res)===0) {
        return false;
    } else {
        return $res;
    }
}


//---------------------------------------------------------------------------------------
