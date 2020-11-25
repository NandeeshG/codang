<?php
require_once 'error.php';
require_once 'utility.php';
require_once 'dbms.php';
require_once 'oauth.php';
require_once 'post.php';
require_once 'get.php';

define('MY_ENV', 'dev');
//reroutes auth code to oauth
if (isset($_GET['auth_rec_route'])) {
    routeError($_GET['auth_rec_route']);
}
//1. This file declares all routes (might wanna split post and get routes among different files).
//2. This file will use get auth from DB or call oauth if env is development or return error if environment is production.
//3. It will use oauth and send request to api by curl file functions. That file shall return error on no auth or no api, whereas wait for rate limit

//$problem = retreiveProblemDetails('SALARY', 'PRACTICE');
//echo $problem['result'];

//$language = retreiveLanguageDetails('c++');
//echo json_encode($language);

$list = array('c++','C++17','JAVA','kyle');
//foreach ($list as $lang) {
    $dbconn = '';
    $trn = transaction("begin", $dbconn);
    logInfo("back in main", $dbconn);
    $qr = addLanguage($list[0], $dbconn);
    echo json_encode($qr);
    if ($trn===false or $qr===false) {
        transaction("rollback", $dbconn);
    } else {
        transaction("commit", $dbconn);
    }
//}

//add dbconn to each dbms function
//make connections to two dbmses in index itself, and pass their conn as var
