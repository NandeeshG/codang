<?php

error_reporting(E_ALL);
$dbconn = pg_connect("host=localhost dbname=codang user=nandeesh password=789&*(") or die('Could not connect to database!!');

function newline($str="")
{
    $endl = php_sapi_name()==='cli' ? "\n" : "<br/>";
    return $str . $endl;
}

//returns false or result on query execution
function my_query($query, &$errstr)
{
    if (!pg_connection_busy($GLOBALS['dbconn'])) {
        pg_send_query($GLOBALS['dbconn'], $query);
    }
    $result = pg_get_result($GLOBALS['dbconn']);
    $res_stat = pg_result_status($result);
    $errstr = $errstr."SQL ERROR! - ".pg_result_error_field($result, PGSQL_DIAG_MESSAGE_PRIMARY).newline().pg_result_error_field($result, PGSQL_DIAG_MESSAGE_DETAIL).newline().pg_result_error_field($result, PGSQL_DIAG_MESSAGE_HINT).newline();
    if ($res_stat==0 || $res_stat==5 || $res_stat==6 ||$res_stat==7) {
        return false;
    } else {
        return $result;
    }
}

function fetchQuery($query, &$errstr=null, $print=false)
{
    $errstr = "";
    $ret = array();
    $qr = my_query($query, $errstr);
    if ($qr===false) {
        $errstr.=newline();
        return false;
    } else {
        $i=0;
        while ($row = pg_fetch_assoc($qr, $i)) {
            $ret[] = $row;
            $i++;
        }
        $errstr.=newline();
        if ($print) {
            foreach ($ret as $rowno => $row) {
                foreach ($row as $key => $value) {
                    echo newline("{$key}-{$value}");
                }
                echo newline();
            }
            echo "since you asked to print - ".$errstr;
        }
        return $ret;
    }
}

function curlRequest($url, $post = false, $headers = array(), $data = array(), $debug=false)
{
    $params = '';
    foreach ($data as $key=>$value) {
        $params .= $key.'='.$value.'&';
    }
    $params = trim($params, '&');
    //http_build_query($params)

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url.'?'.$params); //Url together with parameters
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
    }

    $headers[] = 'content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    if ($debug) {
        die("CURL URL - ".curl_getinfo($ch)['url'].newline());
    }

    if (curl_errno($ch)) {  //catch if curl error exists and show it
        curl_close($ch);
        die('Curl error: ' . curl_error($ch));
    } else {
        curl_close($ch);
        return $response;
    }
}


function authenticationCodeError($response)
{
    if ($response['result']['errors'][0]['message']==='The authorization code has expired' || $response['result']['errors'][0]['message']==="Authorization code doesn't exist or is invalid for the client") {
        return true;
    } elseif ($response['result']['errors']['message']==='The authorization code has expired' || $response['result']['errors']['message']==="Authorization code doesn't exist or is invalid for the client") {
        return true;
    } else {
        return false;
    }
}
function refreshTokenError($response)
{
    if ($response['result']['errors'][0]['message']==='Invalid refresh token') {
        return true;
    } elseif ($response['result']['errors']['message']==='Invalid refresh token') {
        return true;
    } else {
        return false;
    }
}
function authorizationError($response)
{
    if ($response['result']['errors'][0]['message']==='Unauthorized for this resource scope') {
        return true;
    } elseif ($response['result']['errors']['message']==='Unauthorized for this resource scope') {
        return true;
    } else {
        return false;
    }
}
// code 0 - get new auth code
// code 1 - get new access and refresh token
// code 2 - use refresh token to get new AT and RT
function authentication($code)
{
    $errstr="";
    $authDetails = fetchQuery('select * from apiauth', $errstr);
    if (!$authDetails) {
        die("authentication".$errstr);
    }

    if ($code === 0) {
        if (isset($_GET['code'])) {
            $qr = fetchQuery("update apiauth set authorization_code = '{$_GET['code']}'", $errstr, false);
            if ($qr===false) {
                die("code 0 isset".$errstr);
            } else {
                $_GET['code'] = null;
                authentication(1);
                return;
            }
        } else {
            $params = array('response_type'=>'code', 'client_id'=> $authDetails[0]['client_id'], 'redirect_uri'=> $authDetails[0]['redirect_uri'], 'state'=> 'xyz');
            header('Location: ' . $authDetails[0]['authorization_code_endpoint'] . '?' . http_build_query($params));
            die();
        }
    } elseif ($code===1) {
        $oauth_config = array('grant_type' => 'authorization_code', 'code'=> $authDetails[0]['authorization_code'], 'client_id' => $authDetails[0]['client_id'],'client_secret' => $authDetails[0]['client_secret'], 'redirect_uri'=> $authDetails[0]['redirect_uri']);
        $response = json_decode(curlRequest($authDetails[0]['access_token_endpoint'], $oauth_config), true);
        if (authenticationCodeError($response)) {
            authentication(0);
        }
        die(json_encode($response).newline());
        $result = $response['result']['data'];
        $nowtime = time();
        echo("code1 update data".json_encode($response).newline());
        $qr = fetchQuery("update apiauth set gen_time='{$nowtime}', access_token='{$result['access_token']}', refresh_token='{$result['refresh_token']}', scope='{$result['scope']}'", $errstr, true);
        if ($qr===false) {
            die("code1 update".$errstr);
        } else {
            echo("FRESH ACCESS TOKEN AND REFRESH TOKEN GENERATED");
        }
        //die("update apiauth set gen_time='{$nowtime}', access_token='{$result['access_token']}', refresh_token='{$result['refresh_token']}', scope='{$result['scope']}'");
    } elseif ($code===2) {
        $oauth_config = array('grant_type' => 'refresh_token', 'refresh_token'=> $authDetails[0]['refresh_token'], 'client_id' => $authDetails[0]['client_id'],
            'client_secret' => $authDetails[0]['client_secret']);
        $response = json_decode(curlRequest($authDetails[0]['access_token_endpoint'], $oauth_config), true);
        if (refreshTokenError($response) || authenticationCodeError($response)) {
            authentication(1);
            return;
        }
        //die(json_encode($response).newline());

        $result = $response['result']['data'];
        $nowtime = time();
        echo("code2 update data".json_encode($response).newline());
        $qr = fetchQuery("update apiauth set gen_time='{$nowtime}', access_token='{$result['access_token']}', refresh_token='{$result['refresh_token']}', scope='{$result['scope']}'", $errstr);
        if ($qr===false) {
            die("code2 update".$errstr);
        } else {
            echo("REFRESHED THE TOKENS");
        }
    } else {
        die("WRONG ARGUMENTS!");
    }
}

//authentication(2);
$authDetails = fetchQuery('select * from apiauth', $errstr, true);

function make_api_request($access_token, $path, $data = array())
{
    $headers[] = 'Authorization: Bearer ' . $access_token;
    $response = curlRequest($path, false, $headers, $data);
    $response = json_decode($response, true);
    if ($response['status']==='error') {
        echo "RESPONSE ERROR".newline();
        if (authorizationError($response)) {
            authentication(2);
        } else {
            die(json_encode($response).newline()."UNHANDLED ERROR");
        }
        return false;
    } else {
        return $response;
    }
}

function make_contest_problem_api_request()
{
    $authDetails = fetchQuery('select * from apiauth', $errstr);
    if (!$authDetails) {
        die("make contest problem api request".$errstr);
    }
    $problem_code = "SALARY";
    $contest_code = "PRACTICE";
    $path = $authDetails[0]['api_endpoint']."contests/".$contest_code."/problems/".$problem_code;
    $response = make_api_request($authDetails[0]['access_token'], $path);
    $response = json_decode($response, true);
    return $response;
}

//$response = make_contest_problem_api_request();
//die(json_encode($response));



//--------------------------------------------------------------------------------------
