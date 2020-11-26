<?php

require_once 'post.php';
require_once 'get.php';
require_once 'interactWithTest.php';

define('MY_ENV', 'dev');
$dbconn = handleConnect("codang", "open", false);
//reroutes auth code to oauth
if (isset($_GET['auth_rec_route'])) {
    routeError($dbconn, $_GET['auth_rec_route']);
}
$dbconn_test = handleConnect("codang_test", "open", false);
//1. This file declares all routes (might wanna split post and get routes among different files).
//2. This file will use get auth from DB or call oauth if env is development or return error if environment is production.
//3. It will use oauth and send request to api by curl file functions. That file shall return error on no auth or no api, whereas wait for rate limit

//THIS SHOULD ALL CHANGE TO PROBLEM INSTEAD OF CONTEST
while ($cc = codangTestNextContest($dbconn_test, false)) {
    $code = trim($cc[0]['code']);
    $bg = handleTrnsc($dbconn, "begin", false);
    if ($bg===false) {
        die($code."-".$bg);
    }
    $qr = addContestByCode($dbconn, $code, false);
    if ($qr === false) {
        $bg = handleTrnsc($dbconn, "rollback", false);
        if ($bg===false) {
            die($code."-".$bg);
        }
        logInfo("RolledBack - ".$code, $qr);
        die();
    } else {
        $bg = handleTrnsc($dbconn, "commit", false);
        if ($bg===false) {
            die($code."-".$bg);
        }
        logInfo("Commited - ".$code, $qr);
    }
    codangTestMarkContest($dbconn_test, $code, false);
}

//--------------------------------------------------------------------------------
$dbconn = handleConnect($dbconn, "close", false);
$dbconn_test = handleConnect($dbconn_test, "close", false);
