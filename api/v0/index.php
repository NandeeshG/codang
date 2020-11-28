<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';

require_once 'post.php';
require_once 'get.php';
//require_once 'interactWithTest.php';  //for database crawling

if (MY_ENV === PROD) {
    $http_origin = 'http://codang.eastus.cloudapp.azure.com';
} elseif (MY_ENV === DEV) {
    $http_origin = 'http://localhost:3000';
}
$http_origin = '*';

$dbconn = handleConnect("codang", "open", false);
//reroutes auth code to oauth
if (isset($_GET['auth_rec_route'])) {
    routeError($dbconn, $_GET['auth_rec_route']);
}
//1. Routes that call sql functions declared here. sql functions in get.php
//2. Connection to cc api severed now. (original plan was that user will pass their access token to backend and
//    I will use it here to fetch data, but oauth2 failed in nuxtJS so consequently cannot fetch new user data)

$app = AppFactory::create();
//$app->setBasePath("/api/v0/index.php");

$pq = false;

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Welcome to CodangBE!");
    return $response;
});

// GET ALL TAGS - CATEGORY PARAMETER or ALL
$app->get('/categories', function (Request $request, Response $response, $args) {
    $dbconn = $GLOBALS['dbconn'];
    $pq = $GLOBALS['pq'];
    $retval = array('empty');

    //foreach ($params as $k=>$v) {
    //    echo newline($k).newline($v);
    //}

    //$params = $request->getQueryParams();
    //$category = $params['category'];
    //if ($category==false) {
    //    $category = 'all';
    //}

    $bg = handleTrnsc($dbconn, "begin", false);
    if ($bg===true) {
        $qr = getCategories($dbconn, $pq);
    }
    if ($qr === false) {
        $bg = handleTrnsc($dbconn, "rollback", $pq);
    } else {
        $bg = handleTrnsc($dbconn, "commit", $pq);
        if ($bg===true) {
            $retval = $qr;
        }
    }
    $response->getBody()->write(json_encode($retval));
    return $response
          ->withHeader('Content-Type', 'application/json')
          ->withHeader('Access-Control-Allow-Origin', $GLOBALS['http_origin'])
          ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
          ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
          ->withStatus(200);
});
    
// GET ALL TAGS - CATEGORY PARAMETER or ALL
$app->get('/tags', function (Request $request, Response $response, $args) {
    $dbconn = $GLOBALS['dbconn'];
    $pq = $GLOBALS['pq'];


    $params = $request->getQueryParams();
    $category = $params['category'];
    if ($category==false or strcasecmp($category, "all")===0) {
        $category = 'all';
    }

    $retval = array('empty');
    $bg = handleTrnsc($dbconn, "begin", false);
    if ($bg===true) {
        $qr = getTagsByCategory($dbconn, $category, $pq);
    }
    if ($qr === false) {
        $bg = handleTrnsc($dbconn, "rollback", $pq);
    } else {
        $bg = handleTrnsc($dbconn, "commit", $pq);
        if ($bg===true) {
            $retval = $qr;
        }
    }
    $response->getBody()->write(json_encode($retval));

    return $response
          ->withHeader('Content-Type', 'application/json')
          ->withHeader('Access-Control-Allow-Origin', $GLOBALS['http_origin'])
          ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
          ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
          ->withStatus(200);
});

// GET ALL PROBLEMS BY TAG LIST
$app->get('/problemsOR', function (Request $request, Response $response, $args) {
    $dbconn = $GLOBALS['dbconn'];
    $pq = $GLOBALS['pq'];

    $params = $request->getQueryParams();
    $ret = array();
    foreach ($params as $p) {
        foreach ($p as $pp) {
            $ret[] = $pp;
        }
    }

    $retval = array('empty');
    $bg = handleTrnsc($dbconn, "begin", false);
    if ($bg===true) {
        $qr = getProblemsByTagListOR($dbconn, $ret, $pq);
    }
    if ($qr === false) {
        $bg = handleTrnsc($dbconn, "rollback", $pq);
    } else {
        $bg = handleTrnsc($dbconn, "commit", $pq);
        if ($bg===true) {
            $retval = $qr;
        }
    }
    if (strcmp($retval[0], "empty")!==0) {
        foreach ($retval as $q) {
            $q = json_decode($q);
        }
    }

    $response->getBody()->write(json_encode($retval));
    return $response
          ->withHeader('Content-Type', 'application/json')
          ->withHeader('Access-Control-Allow-Origin', $GLOBALS['http_origin'])
          ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
          ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
          ->withStatus(200);
});

// GET ALL PROBLEMS BY TAG LIST
$app->get('/problemsAND', function (Request $request, Response $response, $args) {
    $dbconn = $GLOBALS['dbconn'];
    $pq = $GLOBALS['pq'];

    $params = $request->getQueryParams();
    $ret = array();
    foreach ($params as $p) {
        foreach ($p as $pp) {
            $ret[] = $pp;
        }
    }
    
    $retval = array('empty');
    $bg = handleTrnsc($dbconn, "begin", false);
    if ($bg===true) {
        $qr = getProblemsByTagListAND($dbconn, $ret, $pq);
    }
    if ($qr === false) {
        $bg = handleTrnsc($dbconn, "rollback", $pq);
    } else {
        $bg = handleTrnsc($dbconn, "commit", $pq);
        if ($bg===true) {
            $retval = $qr;
        }
    }

    $response->getBody()->write(json_encode($retval));
    return $response
          ->withHeader('Content-Type', 'application/json')
          ->withHeader('Access-Control-Allow-Origin', $GLOBALS['http_origin'])
          ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
          ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
          ->withStatus(200);
});

$app->run();

//---------------------------------------------------------------------------------------------------------------------
//Script to do database crawling
//Filling submission and language tables is disabled due to stack overflow errors.
//Now that 6000+ problems exists in database, they can be switched on again.
/*
$dbconn_test = handleConnect("codang_test", "open", false);
$pq = false;
$pq2 = false;
while ($pc = codangTestNextProblem($dbconn_test, $pq)) {
    $problemcode = trim($pc[0]['code']);
    $contestcode = trim($pc[0]['contest']);

    ////debug fetching problem
    //$res = fetchProblemByProblemCodeAndContestCode($dbconn, $problemcode, $contestcode, $pq2);
    //logInfo("OUTERMOST FETCH - ", $res);

    $bg = handleTrnsc($dbconn, "begin", false);
    if ($bg == false) {
        logError("begin not possible", $problemcode);
    }
    $qr = addProblemByProblemCodeAndContestCode($dbconn, $problemcode, $contestcode, $pq2);
    if ($qr === false) {
        $bg = handleTrnsc($dbconn, "rollback", $pq);
        if ($bg===false) {
            logError("rollback not possible", $problemcode);
        }
        logInfo("RolledBack - ".$problemcode, $qr);
        sleep(10);
    } else {
        $bg = handleTrnsc($dbconn, "commit", $pq);
        if ($bg===false) {
            logError("commit not possible", $problemcode);
        }
        logInfo("Commited - ".$problemcode, $qr);
    }
    codangTestMarkProblem($dbconn_test, $problemcode, $pq);
}
$dbconn_test = handleConnect($dbconn_test, "close", false);
*/
//--------------------------------------------------------------------------------
$dbconn = handleConnect($dbconn, "close", false);
