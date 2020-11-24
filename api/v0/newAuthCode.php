<?php
require_once 'utility.php';
require_once 'error.php';
require_once 'dbms.php';
require_once 'oauth.php';

// Just used to get new auth code. Redirects to oauth

function getNewAuthCodeFinal($authDetails)
{
    $params = array('response_type'=>'code', 'client_id'=> $authDetails['client_id'], 'redirect_uri'=> $authDetails['redirect_uri'], 'state'=> 'xyz');
    header('Location: ' . $authDetails['authorization_code_endpoint'] . '?' . http_build_query($params));
    die();
}
if (isset($_GET['code'])) {
    $qr = connectAndExecuteQuery("update apiauth set authorization_code = '{$_GET['code']}'");
    if ($qr===false) {
        logError("Cannot update AUTH");
        die();
    } else {
        $params = array('auth_rec_route'=>'New code retreived','authorization_code'=>connectAndExecuteQuery('select authorization_code from apiauth')[0]['authorization_code']);
        //header('Location: ./oauth.php'."?". http_build_query($params));
        header('Location: http://localhost:8080/'."?". http_build_query($params));
        die();
    }
}
