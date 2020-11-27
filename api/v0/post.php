<?php

require_once 'dbms.php';
require_once 'fetch.php';
require_once 'interactWithTest.php';

function addInstitutionByName($dbconn, $institution, $pq=false)
{
    $institution = trim($institution);
    if (strcmp($institution, "")===0) {
        return true;
    }
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

            //if (strcmp(trim($institution), trim($data['institutionName']))!==0) {
            //    logError("Names $institution and {$data['institutionName']} don't match");
            //    return false;
            //}

            $name = pg_escape_literal($dbconn, trim($data['institutionName']));
            $qr = nonTrnscQuery($dbconn, "insert into institution (name) values ($name)", $pq);
            if ($qr === false) {
                logError("Cannot insert institution - ".$institution, $qr);
                return false;
            }
            return getInstitution($dbconn, "name", trim($data['institutionName']), $pq);
        }
    } else {
        echo "Already Exists!"; //logInfo("Already exists - ", $exists);
        return $exists;
    }
}


function addCountryByName($dbconn, $country, $pq=false)
{
    $country = trim($country);
    if (strcmp($country, "")===0) {
        return true;
    }
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
            if (strcmp(trim($country), trim($data['countryName']))!==0) {
                logError("Names $country and {$data['countryName']} don't match");
                return false;
            }
            $ctry = pg_escape_literal($dbconn, trim($data['countryCode']));
            $name = pg_escape_literal($dbconn, trim($data['countryName']));
            $qr = nonTrnscQuery($dbconn, "insert into country (code,name) values ($ctry,$name)", $pq);
            if ($qr === false) {
                logError("Cannot insert country - ".$country, $qr);
                return false;
            }
            return getCountry($dbconn, "name", $country, $pq);
        }
    } else {
        echo "Already Exists!"; //logInfo("Already exists - ", $exists);
        return $exists;
    }
}

// don't insert if isParent but return true
function addContestByCode($dbconn, $contest, $pq=false)
{
    $contest = trim($contest);
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
                logError("Cannot add contests that are parents".$contest, $data);
                return false;
            }
            $sd =   pg_escape_literal($dbconn, strtok(trim($data['startDate']), ' '));
            $ed =   pg_escape_literal($dbconn, strtok(trim($data['endDate']), ' '));
            $name = pg_escape_literal($dbconn, trim($data['name']));
            $annc = pg_escape_literal($dbconn, trim($data['announcements']));
            $bnr =  pg_escape_literal($dbconn, trim($data['bannerFile']));
            $cnt =  pg_escape_literal($dbconn, trim($contest));
            $qr = nonTrnscQuery($dbconn, "insert into contest (code,name,banner,announcement,startdate,enddate) values ($cnt,$name,$bnr,$annc,$sd,$ed)", $pq);
            if ($qr === false) {
                logError("Cannot insert contest - ".$contest, $qr);
                return false;
            }
            return getContestByCode($dbconn, $contest, $pq);
        }
    } else {
        echo "Already Exists!"; //logInfo("Already exists - ", $exists);
        return $exists;
    }
}

function addLanguageByName($dbconn, $language, $pq=false)
{
    $language = trim($language);
    $exists = getLanguageByName($dbconn, $language, $pq);
    if ($exists === false) {
        //fetch from api and add to dbms
        $data = fetchLanguageByName($dbconn, $language, $pq);
        if ($data === false) {
            logError("No data for this language - ".$language, $data);
            return false;
        } else {
            // add to database
            $retr = trim($data['result']['data']['content'][0]['shortName']);
            $lang = trim($language);
            if (strcasecmp($retr, $lang)!==0) {
                logError("Fetched name($retr) don't match with - ".$language, $data);
                return false;
            }
            $retr = pg_escape_literal($dbconn, $retr);
            $qr = nonTrnscQuery($dbconn, "insert into language (name) values ($retr)", $pq);
            if ($qr === false) {
                logError("Cannot insert language - ".$language, $qr);
                return false;
            }
            return getLanguageByName($dbconn, $language, $pq);
        }
    } else {
        echo "Already Exists!"; //logInfo("Already exists - ", $exists);
        return $exists;
    }
}

function addEndUserByName($dbconn, $username, $pq=false)
{
    $username = trim($username);
    $exists = getEndUserByName($dbconn, $username, $pq);
    if ($exists === false) {
        //fetch from api and add to dbms
        $data = fetchEndUserByName($dbconn, $username, $pq);
        if ($data === false) {
            logError("No data for this user - ".$username, $data);
            return false;
        } else {
            $data = $data['result']['data']['content'];
            $fullname = pg_escape_literal($dbconn, trim($data['fullname']));
            if (is_numeric($data['band'][0])) {
                $band = (int)($data['band'][0]);
            } else {
                $band = 0;
            }
            $rating = $data['ratings']['allContest'];
            $country = trim($data['country']['name']);
            $institution = trim($data['organization']);

            //add their country CODE
            $res = addCountryByName($dbconn, $country, $pq);
            if ($res === false or count($res) === 0) {
                logError("Cannot add country - ".$country, $res);
                return false;
            } elseif ($res === true) {
                $country = 'EMPTY';
            } else {
                $country = $res[0]['code'];
            }

            //add their institution CODE
            $res = addInstitutionByName($dbconn, $institution, $pq);
            if ($res === false or count($res) === 0) {
                logError("Cannot add institution".$institution, $res);
                return false;
            } elseif ($res === true) {
                $institution = 0;
            } else {
                $institution = $res[0]['code'];
            }

            // add to database
            $_username = pg_escape_literal($dbconn, $username);
            $fullname = pg_escape_literal($dbconn, trim($data['fullname']));
            $band = pg_escape_literal($dbconn, $band);
            $rating = pg_escape_literal($dbconn, $rating);
            $country = pg_escape_literal($dbconn, $country);
            $institution = pg_escape_literal($dbconn, $institution);

            $qr = nonTrnscQuery($dbconn, "insert into enduser (username,fullname,band,rating,country,institution) values ($_username,$fullname,$band,$rating,$country,$institution)", $pq);
            if ($qr === false) {
                logError("Cannot insert enduser - ".$username, $qr);
                return false;
            }
            
            //$sub = addSubmissionsByUserName($dbconn, $username, $pq);
            //if ($sub === false) {
            //    return false;
            //} else {
            //    return getEndUserByName($dbconn, $username, $pq);
            //}

            $ret = getEndUserByName($dbconn, $username, $pq);
            if ($ret === false) {
                logError("Even after inserting, get user false for - ".$username, $ret);
                return false;
            } else {
                return $ret;
            }
        }
    } else {
        echo "Already Exists!"; //logInfo("Already exists - ", $exists);
        return $exists;
    }
}

//return only true or false
function addSubmissionsByUserName($dbconn, $username, $pq=false)
{
    $username = trim($username);
    // only query submissions after last query
    do {
        $afterid = getSubmissionByName($dbconn, $username, $pq, "oldest");
        $data = fetchSubmissionByUserNameAfterId($dbconn, $username, $afterid, $pq);

        if ($data === false) {
            logError("No submissions for this user - ".$username, $data);
            return false;
        } elseif ($data['result']['data']['code']===9003) {
            return true;
        } else {
            $list = $data['result']['data']['content'];

            foreach ($list as $data) {
                $_id = pg_escape_literal($dbconn, $data['id']);
                $sourcecode = pg_escape_literal($dbconn, "NOT AVAILABLE"); //from another api call again
                $memory = pg_escape_literal($dbconn, $data['memory']);
                $result = pg_escape_literal($dbconn, trim($data['result']));
                $score = pg_escape_literal($dbconn, $data['score']);
                $time = pg_escape_literal($dbconn, $data['time']);
                $date = pg_escape_literal($dbconn, strtok(trim($data['date']), ' '));
                $link = pg_escape_literal($dbconn, "");
                $language = trim($data['language']);
                $problemcode = trim($data['problemCode']);
                $contestcode = trim($data['contestCode']);

                $getProblem = getProblemByCode($dbconn, $problemcode, $pq);
                if ($getProblem===false) {
                    //add the problem depending on env
                    //if (MY_ENV === DEV) {
                    //    $temp = handleConnect("codang_test", "open", false);
                    //    if ($temp === false) {
                    //        logError("Cannot open handle ", $data);
                    //        continue;
                    //    }
                    //    $res = codangTestAddProblem($temp, $problemcode, $contestcode, $pq);
                    //    if ($res === false) {
                    //        logError("Cannot add problem to codang ", $data);
                    //        continue;
                    //    }
                    //    handleConnect($temp, "close", false);
                    //} elseif (MY_ENV === PROD) {
                    $res = addProblemByProblemCodeAndContestCode($dbconn, $problemcode, $contestcode, $pq);
                    if ($res === false or count($res) === 0) {
                        continue; //because we wan't to add other submissions
                    }
                    //}
                }

                //SKIP FOR NOW
                //add the language
                //$res_lan = addLanguageByName($dbconn, $language, $pq);
                //if ($res_lan===false) {
                //    continue;
                //}
                $res_lan = addLanguageByName($dbconn, "ANY", $pq);
                if ($res_lan===false) {
                    logError("Cannot add temporary problemlanguagelink - ".$problemcode."-"."0", $res_lan);
                }

                $language = pg_escape_literal($dbconn, $res_lan[0]['code']);
                $problemcode = pg_escape_literal($dbconn, $res[0]['code']);
                $contestcode = pg_escape_literal($dbconn, $res[0]['contestcode']);

                $qr = nonTrnscQuery($dbconn, "insert into submission (id,sourcecode,memory,result,date,time,score,link,username,languagecode,problemcode,contestcode) values ($_id,$sourcecode,$memory,$result,$date,$time,$score,$link,$username,$language,$problemcode,$contestcode)", $pq);
                if ($qr === false) {
                    logError("Cannot insert enduser - ".$username, $qr);
                    continue;
                }
            }
        }
    } while (true);
    return true;
}

function addProblemByProblemCodeAndContestCode($dbconn, $problemcode, $contestcode, $pq)
{
    $problemcode = trim($problemcode);
    $contestcode = trim($contestcode);
    $exists = getProblemByCode($dbconn, $problemcode, $pq);
    if ($exists === false) {
        //fetch from api and add to dbms
        $data = fetchProblemByProblemCodeAndContestCode($dbconn, $problemcode, $contestcode, $pq);
        if ($data === false) {
            logError("No data for this problem - ".$problemcode, $data);
            return false;
        } else {
            $data = $data['result']['data']['content'];

            // non-hooks
            $problemcode = $problemcode;
            $name = pg_escape_literal($dbconn, trim($data['problemName']));
            $date = pg_escape_literal($dbconn, trim($data['dateAdded']));
            $maxtimelimit = $data['maxTimeLimit'];
            $sourcesizelimit = $data['sourceSizeLimit'];
            $body = pg_escape_literal($dbconn, trim($data['body']));

            // hooks
            $contestcode_ = $contestcode;
            $author = trim(($data['author']));
            $langArr = $data['languagesSupported'];
            $tagArr = $data['tags'];

            //add their contest code
            $res = addContestByCode($dbconn, $contestcode, $pq);
            if ($res === false or count($res) === 0) {
                logError("Cannot add contestcode - ".$contestcode);
                return false;
            }
            
            //add their author
            $res = addEndUserByName($dbconn, $author, $pq);
            if ($res === false or count($res) === 0) {
                logError("Cannot add user - ".$author, $res);
                return false;
            }

            //SKIP FOR NOW
            ////add their languages and many-many-relation
            //// NOTE - many-many relation should come after problem is inserted
            //$lc = array();
            //foreach ($langArr as $language) {
            //    $languageRes = addLanguageByName($dbconn, $language, $pq);
            //    if ($languageRes === false or count($languageRes) === 0) {
            //        logError("Cannot add language - ".$language);
            //        return false;
            //    } else {
            //        $lc[] = $languageRes[0]['code'];
            //    }
            //}

            //add their tags
            $tc = array();
            foreach ($tagArr as $tag) {
                $res = addTagByNamePublic($dbconn, $tag, $pq);
                if ($res === false or count($res) === 0) {
                    logError("Cannot add tag - ".$tag);
                    return false;
                } else {
                    $tc[] = $res[0]['code'];
                }
            }

            // add to database
            $_problemcode = pg_escape_literal($dbconn, $problemcode);
            $_contestcode = pg_escape_literal($dbconn, $contestcode);
            $_author = pg_escape_literal($dbconn, $author);

            $qr = nonTrnscQuery($dbconn, "insert into problem (code,name,date,maxtimelimit,sourcesizelimit,body,contestcode,author) values ($_problemcode,$name,$date,$maxtimelimit,$sourcesizelimit,$body,$_contestcode,$_author)", $pq);
            if ($qr === false) {
                logError("Cannot insert problem - ".$problemcode, $qr);
                return false;
            }

            //language temporary fix
            $res2 = addProblemLanguageByCodes($dbconn, $problemcode, 0, $pq);
            if ($res2 === false or count($res2) === 0) {
                logError("Cannot add temporary problemlanguagelink - ".$problemcode."-"."0", $res2);
                return false;
            }

            ////language many-many relation
            //foreach ($lc as $lcc) {
            //    $res2 = addProblemLanguageByCodes($dbconn, $problemcode, $lcc, $pq);
            //    if ($res2 === false or count($res2) === 0) {
            //        logError("Cannot add problemlanguagelink - ".$problemcode."-".$lcc."-".$language);
            //        return false;
            //    }
            //}

            //tag many-many relation
            foreach ($tc as $tcc) {
                $_tagcode = pg_escape_literal(trim($tcc));
                $exists = nonTrnscQuery($dbconn, "select * from problemtag where problemcode=$_problemcode and tagcode=$_tagcode", $pq);
                if ($exists === false or count($exists) === 0) {
                    $insert = nonTrnscQuery($dbconn, "insert into problemtag (problemcode,tagcode) values ($_problemcode, $_tagcode)", $pq);
                    if ($insert === false) {
                        logError("Cannot insert problemtag relationship - ".$problemcode."-".$tcc, $insert);
                        return false;
                    }
                }
            }

            return getProblemByCode($dbconn, $problemcode, $pq);
        }
    } else {
        echo "Already Exists!"; //logInfo("Already exists - ", $exists);
        return $exists;
    }
}

function addProblemLanguageByCodes($dbconn, $problemcode, $languagecode, $pq)
{
    $_problemcode = pg_escape_literal($dbconn, trim($problemcode));
    $_languagecode = pg_escape_literal($dbconn, trim($languagecode));

    $exists = getProblemLanguageByCodes($dbconn, $problemcode, $languagecode, $pq);
    if ($exists === false) {
        //add to dbms
        $qr = nonTrnscQuery($dbconn, "insert into problemlanguage (problemcode,languagecode) values ($_problemcode,$_languagecode)", $pq);
        if ($qr === false) {
            logError("Cannot insert problem - language".$problemcode."-".$languagecode);
            return false;
        } else {
            $exists = getProblemLanguageByCodes($dbconn, $problemcode, $languagecode, $pq);
        }
    }

    if ($exists === false) {
        logError("Insertion of language failed - ".$problemcode."-".$languagecode);
        return false;
    } else {
        return $exists;
    }
}

//for particular user as owner -> add tag name and owner referenced to username
//add the given problem to problemtag category
function addTagByNameToProblemsWithOwner($dbconn, $tag, $owner, $problemcodelist, $pq=false)
{
    $tag = trim($tag);
    $owner = trim($owner);
    $exists = getTagByNameByOwner($dbconn, $tag, $owner, $pq);
    //if exists only add new problems if they too exist and don't have relation currently
    //else add tag with tagname and owner, given the user exists (if not return false)
    if ($exists === false) {
        $res = getEndUserByName($dbconn, $owner, $pq);
        if ($res === false) {
            logError("User doesn't exist for tag - ".$tag, $pq);
            return false;
        }

        //add tag name and owner to db
        $tagname = pg_escape_literal($dbconn, $tag);
        $ownername = pg_escape_literal($dbconn, $owner);
        $res = nonTrnscQuery($dbconn, "insert into tag (name,owner) values ($tagname, $ownername)", $pq);
        if ($res === false) {
            logError("Cannot insert tag - ".$tagname, $res);
            return false;
        }
    }
    $res = getTagByNameByOwner($dbconn, $tag, $owner, $pq);
    if ($res === false) {
        logError("FATAL ERROR!! Get failed even after insert!! Cannot find tag - ".$tagname, $res);
        return false;
    }
    $tagcode = $res[0]['code'];
    foreach ($problemcodelist as $problemcode) {
        //add their problem codes if exists
        $res = getProblemByCode($dbconn, $problemcode, $pq);
        if ($res === false or count($res) === 0) {
            logInfo("Problem not in database yet".$problemcode, $res);
            continue;
        }

        $_problemcode = pg_escape_literal($dbconn, trim($problemcode));
        //add to problemtag
        $res = nonTrnscQuery($dbconn, "insert into problemtag (problemcode,tagcode) values ($_problemcode,$tagcode)", $pq);
        if ($res == false) {
            logError("Tag and Problem exist but cannot insert relationship - ".$tagcode."-".$problemcode, $pq);
            return false;
        }
    }
    return $res;
}

//for owner public only
function addTagByNamePublic($dbconn, $tag, $pq="false")
{
    //SKIP FETCHING Tag's problems, only return the tag.
    $tag = trim($tag);
    $exists = getTagByNameByOwner($dbconn, $tag, "public", $pq);
    if ($exists === false) {

        //add tag name and owner to db
        $tagname = pg_escape_literal($dbconn, $tag);
        $ownername = pg_escape_literal($dbconn, "public");
        $res = nonTrnscQuery($dbconn, "insert into tag (name,owner) values ($tagname, $ownername)", $pq);
        if ($res === false) {
            logError("Cannot insert tag - ".$tagname, $res);
            return false;
        }

        //get its category (only if public) (also add)
        $addtag = addCategoryWithTagName($dbconn, $tag, $pq); //returns the category row
        if ($addtag === false) {
            logError("Cannot add tag category", $addtag);
            return false;
        }

        //add tagCategory
        $tagret = nonTrnscQuery($dbconn, "select * from tag where name=$tagname and owner=$ownername", $pq);
        $tagcode = pg_escape_literal($dbconn, trim($tagret[0]['code']));
        $catcode = pg_escape_literal($dbconn, trim($addtag[0]['code']));
        $addTagCat = nonTrnscQuery($dbconn, "insert into tagcategory (tagcode,categorycode) values ($tagcode, $catcode)", $pq);
        if ($addTagCat === false) {
            logError("Cannot insert tag ".$tagcode."-".$catcode, $addTagCat);
            return false;
        }

        return getTagByNameByOwner($dbconn, $tag, "public", $pq);
        
    //Actually skip is the only way, because if we are not fetching from api, and for updating in present database,
        //then they are already present so they will have links to this tag.

        //SKIP FETCHING Tag's problems, only return the tag.

        ////fetch problems from api and add to dbms
        //$offset = 0;
        //do {
        //    //fetch all its problems and add them
        //    $data = fetchTagByNameWithOffset($dbconn, $tag, $offset, $pq); //gets first tag or false
        //    //fetch tag automatically increases offset for its next call
        //    if ($data === false) {
        //        logError("No data for this tag - ".$tag, $data);
        //        return false;
        //    } elseif ($data['result']['data']['code'] === 9003) {
        //        return $tagret;
        //    } else {
        //        $probAssoc = $data['result']['data']['content'];

        //        foreach ($probAssoc as $problemcode=>$problemdetails) {
        //            //add their problem codes if exists
        //            // LIMITATION OF CCAPI - since it doesn't return contestcode for the problem, I cannot fetch
        //            // its details if problem doesn't exist. so use get. (but not a big problem, as that problem will
        //            // arrive now or later anyway)
        //            $res = getProblemByCode($dbconn, $problemcode, $pq);
        //            if ($res === false or count($res) === 0) {
        //                logInfo("Problem not in database yet".$problemcode, $res);
        //                continue;
        //            }

        //            $_problemcode = pg_escape_literal($dbconn, trim($problemcode));
        //            //add to problemtag
        //            $res = nonTrnscQuery($dbconn, "insert into problemtag (problemcode,tagcode) values ($_problemcode,$tagcode)", $pq);
        //            if ($res == false) {
        //                logError("Tag and Problem exist but cannot insert relationship - ".$tagcode."-".$problemcode, $pq);
        //                return false;
        //            }
        //        }
        //    }
        //} while (true);
    } else {
        echo "Already Exists!"; //logInfo("Already exists - ", $exists);
        return $exists;
    }
}

function addCategoryWithTagName($dbconn, $tag, $pq)
{
    $tag = trim($tag);
    //returns the category row
    $category = false;

    $tempoffset=0;
    $tagfetch = fetchTagByNameWithOffset($dbconn, $tag, $tempoffset, $pq);
    if ($tagfetch === false) {
        logError("Tag doesn't exists with codechef. ".$tag, $tagfetch);
        return false;
    }

    //check if tag is contest
    if ($category === false) {
        //check local first to avoid api calls
        $contest = getContestByCode($dbconn, $tag, $pq);
        if ($contest!==false and count($contest)!==0) {
            $category = "Contest";
        } else {
            $contest = fetchContestByCode($dbconn, $tag, $pq);
            if ($contest!==false and count($contest)!==false) {
                $category = "Contest";
            }
        }
    }

    //check words = easy medium hard cakewalk simple school challenge
    if ($category === false) {
        $listToCheck = ["easy","medium","hard","cakewalk","school","simple","challenge"];
        foreach ($listToCheck as $str) {
            if (strpos($tag, $str)!==false) {
                $category = "Difficulty";
                break;
            }
        }
    }

    //check if tag is author from retreiving first tag problem and comparing its author
    if ($category === false) {
        $probdetails = $tagfetch['result']['data']['content'];
        //logInfo("Problemdetails for tag ".$tag, $probdetails);
        foreach ($probdetails as $key=>$value) {
            $author = trim($value['author']);
            $user = getEndUserByName($dbconn, $author, $pq);
            if ($user === false or count($user)===0) {
                $user = fetchEndUserByName($dbconn, $author, $pq);
            }

            if (strcmp($author, $tag)!==0) {
                break;
            }

            if ($user !== false and count($user)!==0) {
                $band = $user['result']['data']['content']['band'];
                if (strcasecmp($band, "UnRated")!==0 and (int)$band[0] > 1) {
                    $category = "Author";
                } elseif (strpos($author, "adm")!==false) {
                    $category = "Author";
                }
            }
            break;
        }
    }

    //categorize as concept in end
    if ($category === false) {
        $category = "Concept";
    }

    logInfo("Tag matched to - ".$category);
    
    //add category if not already exists
    $exists = getCategoryByName($dbconn, $category, $pq);
    if ($exists === false) {
        // add to database

        $_category = pg_escape_literal($dbconn, trim($category));
        $qr = nonTrnscQuery($dbconn, "insert into category (name) values ($_category)", $pq);
        if ($qr === false) {
            logError("Cannot insert category - ".$category, $qr);
            return false;
        }
        return getCategoryByName($dbconn, $category, $pq);
    } else {
        echo "Already Exists!"; //logInfo("Already exists - ", $exists);
        return $exists;
    }
}
//------------------------------------------------------------------------------------------------
