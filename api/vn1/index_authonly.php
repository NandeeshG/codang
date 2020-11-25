<?php

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
    echo json_encode($response)."<br/>";
    $result = $response['result']['data'];

    $oauth_details['access_token'] = $result['access_token'];
    $oauth_details['refresh_token'] = $result['refresh_token'];
    $oauth_details['scope'] = $result['scope'];

    return $oauth_details;
}

function generate_access_token_from_refresh_token($config, $oauth_details)
{
    $oauth_config = array('grant_type' => 'refresh_token', 'refresh_token'=> $oauth_details['refresh_token'], 'client_id' => $config['client_id'],
        'client_secret' => $config['client_secret']);
    $response = json_decode(make_curl_request($config['access_token_endpoint'], $oauth_config), true);
    echo json_encode($response)."<br/>";
    $result = $response['result']['data'];

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
    curl_close($ch);

    if (curl_errno($ch)) {  //catch if curl error exists and show it
        die('Curl error: ' . curl_error($ch));
    } else {
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

function main()
{
    $config = array('client_id'=> '5cd537499b6a22a4898dbcb9e68c6783',
        'client_secret' => '9a6b652f2bd4dbd97a451b1125984007',
        'api_endpoint'=> 'https://api.codechef.com/',
        'authorization_code_endpoint'=> 'https://api.codechef.com/oauth/authorize',
        'access_token_endpoint'=> 'https://api.codechef.com/oauth/token',
        'redirect_uri'=> 'http://localhost:8080/',
        'website_base_url' => 'http://localhost:8080/');

    $oauth_details = array('authorization_code' => '4f72285b715140c59e55065c0e4a95d33cbbd1ff',
        'access_token' => '5426955994b181ce417386162079f1473957cba4',
        'refresh_token' => 'b41e9a6a6f41c8420c415550d5c8e3b7e1a491ba');

    $_GET['code'] = null;

    if (isset($_GET['code'])) {
        //$oauth_details['authorization_code'] = $_GET['code'];
        //$oauth_details = generate_access_token_first_time($config, $oauth_details);

        //print details
        echo "<hr/>";
        foreach ($oauth_details as $key => $value) {
            echo $key . "=>" . $value . "<br/>";
        }
        echo "<hr/>";

        $response = make_contest_problem_api_request($config, $oauth_details);
        echo $response['result']['data']['content']['body'];

        $response = get_contest_list($config, $oauth_details);
        foreach ($response as $key => $value) {
            echo $key . "=" . $value . "<br/>";
        }
        
        //$oauth_details = generate_access_token_from_refresh_token($config, $oauth_details);         //use this if you want to generate access_token from refresh_token
    } else {
        take_user_to_codechef_permissions_page($config);
    }
}

main();
