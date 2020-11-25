<?php
//while (@ob_end_flush());
////slim
//use Psr\Http\Message\ServerRequestInterface as Request;
//use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
//use Slim\Factory\AppFactory;
//use Psr\Http\Message\ResponseInterface as Response;
//require __DIR__ . '/../vendor/autoload.php';
//$app = AppFactory::create();

error_reporting(E_ALL);
//only for dev env
ini_set('display_errors', 1);
//ini_set('log_errors',1);

//connect
$dbconn = pg_connect("host=localhost dbname=codang_test user=nandeesh password=789&*(") or die('Could not connect: '.pg_last_error());

function my_query($query, &$errstr)
{
    if (!pg_connection_busy($GLOBALS['dbconn'])) {
        pg_send_query($GLOBALS['dbconn'], $query);
    }
    $result = pg_get_result($GLOBALS['dbconn']);
    $res_stat = pg_result_status($result);
    $errstr = $errstr.pg_result_error_field($result, PGSQL_DIAG_MESSAGE_PRIMARY)."\n";
    if ($res_stat==0 || $res_stat==5 || $res_stat==6 ||$res_stat==7) {
        return false;
    } else {
        return $result;
    }
}

function take_user_to_codechef_permissions_page($config)
{
    $params = array('response_type'=>'code', 'client_id'=> $config['client_id'], 'redirect_uri'=> $config['redirect_uri'], 'state'=> 'xyz');
    header('Location: ' . $config['authorization_code_endpoint'] . '?' . http_build_query($params));
    die();
}

function generate_access_token_first_time($config, $oauth_details)
{
    $oauth_config = array('grant_type' => 'authorization_code', 'code'=> $oauth_details['authorization_code'], 'client_id' => $config['client_id'],
                          'client_secret' => $config['client_secret'], 'redirect_uri'=> $config['redirect_uri']);
    $response = json_decode(make_curl_request($config['access_token_endpoint'], $oauth_config), true);
    echo json_encode($response)."\n";
    $result = $response['result']['data'];

    $oauth_details['gentime'] = time();
    $oauth_details['access_token'] = $result['access_token'];
    $oauth_details['refresh_token'] = $result['refresh_token'];
    $oauth_details['scope'] = $result['scope'];

    return $oauth_details;
}


function generate_access_token_from_refresh_token($config, &$oauth_details)
{
    $oauth_config = array('grant_type' => 'refresh_token', 'refresh_token'=> $oauth_details['refresh_token'], 'client_id' => $config['client_id'],
        'client_secret' => $config['client_secret']);
    $response = json_decode(make_curl_request($config['access_token_endpoint'], $oauth_config), true);
    echo json_encode($response);
    if ($response['result']['errors']['message'] === 'Invalid refresh token') {
        take_user_to_codechef_permissions_page($config);
        $oauth_details['authorization_code'] = $_GET['code'];
        $oauth_details = generate_access_token_first_time($config, $oauth_details);
        echo "\n";
        foreach ($oauth_details as $key => $value) {
            echo $key . "=>" . $value . "\n";
        }
        echo "\n";
        flush();
    }
    $result = $response['result']['data'];

    $oauth_details['gentime'] = time();
    $oauth_details['access_token'] = $result['access_token'];
    $oauth_details['refresh_token'] = $result['refresh_token'];
    $oauth_details['scope'] = $result['scope'];

    return $oauth_details;
}

function make_curl_request($url, $post = false, $headers = array(), $data = array())
{
    $params = '';
    foreach ($data as $key=>$value) {
        $params .= $key.'='.$value.'&';
    }
    $params = trim($params, '&');

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

    if (curl_errno($ch)) {  //catch if curl error exists and show it
        curl_close($ch);
        die('Curl error: ' . curl_error($ch));
    } else {
        curl_close($ch);
        return $response;
    }
}

function make_api_request($oauth_config, $path, $data = array())
{
    $headers[] = 'Authorization: Bearer ' . $oauth_config['access_token'];
    return make_curl_request($path, false, $headers, $data);
}

function make_contest_problem_api_request($config, $oauth_details)
{
    $problem_code = "SALARY";
    $contest_code = "PRACTICE";
    $path = $config['api_endpoint']."contests/".$contest_code."/problems/".$problem_code;
    $response = make_api_request($oauth_details, $path);
    $response = json_decode($response, true);
    return $response;
}

function make_contest_list_api_request($config, $oauth_details)
{
    $path = $config['api_endpoint']."contests/";
    $params = array('status'=>'past', 'limit'=>'3000');
    $response = make_api_request($oauth_details, $path, $params);
    $response = json_decode($response, true);
    return $response;
}

function get_contest_list($config, $oauth_details)
{
    $response = make_contest_list_api_request($config, $oauth_details);
    $contestarr = $response['result']['data']['content']['contestList'];
    $codes = array();
    foreach ($contestarr as $key => $value) {
        $codes[] = $value['code'];
    }
    return $codes;
}

function get_problem_list_from_contest_db($config, &$oauth_details)
{
    //query dbms to get contest list
    $err = "";
    $qr = my_query('select * from contest', $err);
    if (!$qr) {
        die($err);
    } else {
        $contestlist = array();
        while ($row = pg_fetch_row($qr)) {
            $contestlist[] = $row;
        }
    }

    foreach ($contestlist as $key => $value) {
        if ((time()-(int)($oauth_details['gentime'])) > (3500)) {
            echo "I SAY TOKEN EXPIRED \n";
            $oauth_details = generate_access_token_from_refresh_token($config, $oauth_details);
            echo "NEW TOKEN GENERATED!!!";
            echo "\n";
            foreach ($oauth_details as $key => $value) {
                echo $key . "=>" . $value . "\n";
            }
            echo "\n";
        }

        $ct = $value[0];
        $qr = my_query("select done from contest where code='{$ct}'", $err);
        $val = pg_fetch_assoc($qr);
        if (!$qr) {
            echo $err;
        } elseif ($val['done'] === '1') {
            echo $ct . "  " . $val['done'] . " skipped \n";
            continue;
        }

        echo json_encode($ct) . "!!!! ";
        flush();
        $path = $config['api_endpoint']."contests/".$ct;
        $response = make_api_request($oauth_details, $path);
        $response = json_decode($response, true);
        echo json_encode($response['status']) . "\n";
        flush();
        if ($response['status']!=='OK') {
            echo json_encode($response) . "\n";
            if ($response['status']==='FORBIDDEN') {
                continue;
            } elseif ($response['result']['errors'][0]['message']!=='API request limit exhausted') {
                die("WAS TRYING FOR REFRESH TOKEN");
                $oauth_details = generate_access_token_from_refresh_token($config, $oauth_details);
                echo "NEW TOKEN GENERATED!!!";
            } else {
                echo "SLEEPING NOW at ". date('H:i:s') . "....... \n ";
                flush();
                sleep(5*60);
                echo "I AM AWAKE AGAIN!! \n";
                flush();
            }
            $response = make_api_request($oauth_details, $path);
            $response = json_decode($response, true);
        }
        flush();
        $problemsList = $response['result']['data']['content']['problemsList'];

        $count = 0;
        foreach ($problemsList as $key => $value) {
            $count++;
        }
        if ($count===0) {
            $qr = my_query("update contest set done=1 where code='{$ct}'", $err);
            if (!$qr) {
                die($err);
            } else {
                echo "INSERTED CONTEST " . $ct . "\n";
            }
            flush();
            continue;
        }

        foreach ($problemsList as $key => $value) {
            //$prbstr = "";
            //$prbcode = $value['problemCode'];
            //$prbstr .= "('{$prbcode}'),";
            //$prbstr = trim($prbstr, ',');
            //echo "{$prbstr}";
            $qr = my_query("insert into problem (code,contest) values ('{$value['problemCode']}','{$ct}')", $err);
            echo "insert into problem (code,contest) values ('{$value['problemCode']}','{$ct}') \n";
            if (!$qr) {
                flush();
                echo($err);
            }
        }
        flush();

        $qr = my_query("update contest set done=1 where code='{$ct}'", $err);
        if (!$qr) {
            die($err);
        } else {
            echo "INSERTED CONTEST " . $ct . "\n";
        }
        flush();
    }
}

function main()
{
    $config = array('client_id'=> '5cd537499b6a22a4898dbcb9e68c6783',
        'client_secret' => '9a6b652f2bd4dbd97a451b1125984007',
        'api_endpoint'=> 'https://api.codechef.com/',
        'authorization_code_endpoint'=> 'https://api.codechef.com/oauth/authorize',
        'access_token_endpoint'=> 'https://api.codechef.com/oauth/token',
        'redirect_uri'=> 'http://localhost:8080/',
        'website_base_url' => 'http://localhost:8080/');

    $oauth_details = array('authorization_code' => 'c226fc1c799df17379ab9324467ddfce27b178cc',
        'access_token' => '185257ef1fef92616f7845170a3f3fe7c54d6ae1',
        'refresh_token' => '9cc15b743091e8a19f2cd2f2f05f9558d18786a6',
        'gentime' => '1606043444');

    //$_GET['code'] = null;

    //if (!isset($_GET['code'])) {
    //    take_user_to_codechef_permissions_page($config);
    //}
    //$oauth_details['authorization_code'] = $_GET['code'];

    //$oauth_details = generate_access_token_first_time($config, $oauth_details);
    //$oauth_details = generate_access_token_from_refresh_token($config, $oauth_details);         //use this if you want to generate access_token from refresh_token

    //print details
    echo "\n";
    foreach ($oauth_details as $key => $value) {
        echo $key . "=>" . $value . "\n";
    }
    echo "\n";
    flush();

    //$response = make_contest_problem_api_request($config, $oauth_details);
    //echo $response['result']['data']['content']['body'];

    ////FILL DBMS WITH CONTEST CODES
    //$response = get_contest_list($config, $oauth_details);
    //foreach ($response as $key => $value) {
    //    $errstr = "";
    //    $qr = my_query("insert into contest (code) values ('{$value}')", $errstr);
    //    if (!$qr) {
    //        echo $errstr . "\n";
    //    }
    //    //echo $key . "=" . $value . "\n";
    //}
    //echo "INSERTED SUCCESS" . "\n";

    get_problem_list_from_contest_db($config, $oauth_details);

    echo "\n";
    foreach ($oauth_details as $key => $value) {
        echo $key . "=>" . $value . "\n";
    }
    echo "\n";
    flush();
}

main();

//disconnect
pg_close($dbconn);
