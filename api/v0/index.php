<?php
require_once 'error.php';
require_once 'utility.php';
require_once 'dbms.php';
require_once 'oauth.php';

define('MY_ENV', 'dev');

//1. This file declares all routes (might wanna split post and get routes among different files).
//2. This file will use get auth from DB or call oauth if env is development or return error if environment is production.
//3. It will use oauth and send request to api by curl file functions. That file shall return error on no auth or no api, whereas wait for rate limit

//reroutes auth code to oauth
if (isset($_GET['auth_rec_route'])) {
    routeError($_GET['auth_rec_route']);
}
$api_endpoint = getAuthDetails()['api_endpoint'];

function testCallback($str1, $str2, $str3)
{
    echo json_encode(connectAndExecuteQuery('select * from apiauth')).newline();
    die(newline($str1).newline($str2).newline($str3));
}

logInfo("getauth", getAuthDetails());
//routeError("Invalid refresh token");
//die();

$contest = 'LTIME88A';
$problem = 'WATMELON';
//for ($i=0; $i<100; $i++) {
    $response = codeChefGet($api_endpoint."contests/{$contest}/problems/{$problem}/", array());
    logInfo("CCGET - ", $response);
//}
