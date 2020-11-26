<?php
require_once 'error.php';

// TrnsQuery - returns array of associated arrays
// use double loop to use the return value
// use json_encode(ret) to print
//example usage of return
//$ret = (withTrnscQuery($dbconn,"select * from country"));
//foreach ($ret as $value) {
//    echo newline($value['name']).newline($value['code']).newline();
//    //foreach ($value as $k=>$v) {
//    //    echo $k."--".$v.newline();
//    //}
//}
//echo json_encode(connectAndExecuteQuery('select * from country'));

//returns unformatted result
function smallQuery($dbconn, $query, $pq=PRINT_QUERY)
{
    if ($dbconn === false) {
        logError("DBCONN INVALID - ", $dbconn);
        return false;
    }

    if (pg_connection_status($dbconn)==PGSQL_CONNECTION_BAD) {
        logError("NO CONNECTION", $dbconn);
        return false;
    }

    if (!pg_connection_busy($dbconn)) {
        $send = pg_send_query($dbconn, $query);
        if ($send===false) {
            logError("Couldn't send query to database!", $send);
            return false;
        }
        if ($pq === true) {
            logInfo("sent query - ", $send);
        }
    } else {
        logError("db busy!", $dbconn);
        return false;
    }

    $result = pg_get_result($dbconn);
    if ($result === false) {
        logError("Couldn't retrieve query result", $result);
        return false;
    }
    if ($pq === true) {
        logInfo("result of query - ".$query, $result);
    }

    return $result;
}
function nonTrnscQuery($dbconn, $query, $pq=PRINT_QUERY)
{
    if ($dbconn === false) {
        logError("DBCONN INVALID - ", $dbconn);
        return false;
    }

    $result = smallQuery($dbconn, $query, $pq);
    if ($pq===true) {
        logInfo("small query gave - ", $result);
    }
    if ($result === false) {
        logError("Result was false - ", $result);
        return false;
    } else {
        $res_stat = pg_result_status($result);
        $errstr = "SQL ERROR! (id-$res_stat)(2 means ok) -> ".pg_result_error_field($result, PGSQL_DIAG_MESSAGE_PRIMARY).newline().pg_result_error_field($result, PGSQL_DIAG_MESSAGE_DETAIL).newline().pg_result_error_field($result, PGSQL_DIAG_MESSAGE_HINT).newline();
        if ($pq===true) {
            logInfo("result status is - ".$errstr, $result);
        }
        if ($res_stat==0 || $res_stat==5 || $res_stat==6 ||$res_stat==7) {
            logError($errstr, $result);
            return false;
        } else {
            $ret = array();
            while ($row = pg_fetch_assoc($result)) {
                $ret[] = $row;
            }
            //$i=0;
            //while ($row = pg_fetch_assoc($result, $i)) {
            //    $ret[] = $row;
            //    $i++;
            //}
            if ($pq === true) {
                $prstr = "QUERY - ".newline($query);
                foreach ($ret as $rowno => $row) {
                    foreach ($row as $key => $value) {
                        $prstr .= newline("{$key}-{$value}");
                    }
                }
                logInfo($prstr, $result);
            }
            return $ret;
        }
    }
}
function withTrnscQuery($dbconn, $query, $pq=PRINT_QUERY)
{
    if ($dbconn === false) {
        logError("DBCONN INVALID - ", $dbconn);
        return false;
    }

    $begin = nonTrnscQuery($dbconn, "BEGIN", $pq);
    if ($begin === false) {
        logError("Cannot BEGIN - ", $begin);
        return false;
    }
    if ($pq === true) {
        logInfo("Transaction begun - ", $begin);
    }

    $result = nonTrnscQuery($dbconn, $query, $pq);
    if ($pq === true) {
        logInfo("Result of query - ", $result);
    }

    if ($result === false) {
        $rb = nonTrnscQuery($dbconn, "ROLLBACK", $pq);
        if ($pq === true) {
            logInfo("Transaction ROLLBACK - ", $begin);
        }
        logError("Cannot do query - ", $result);
        if ($rb === false) {
            logError("Cannot ROLLBACK - ", $rb);
        }
        return false;
    } else {
        $commit = nonTrnscQuery($dbconn, "COMMIT", $pq);
        if ($pq === true) {
            logInfo("Transaction COMMIT - ", $begin);
        }
        if ($commit === false) {
            if ($commit === false) {
                logError("Cannot COMMIT - ", $rb);
            }
            return false;
        }
        return $result;
    }
}
function handleTrnsc($dbconn, $command, $pq=PRINT_QUERY)
{
    if ($dbconn === false) {
        logError("DBCONN INVALID - ", $dbconn);
        return false;
    }
    
    if (strcmp($command, "begin")!==0 and strcmp($command, "rollback")!==0 and strcmp($command, "commit")!==0) {
        logError("WRONG ARGS - ", $command);
        return false;
    }

    $bg = nonTrnscQuery($dbconn, $command, $pq);
    if ($pq === true) {
        logInfo("$command result - ", $bg);
    }
    if ($bg === false) {
        logError("$command failed - ", $bg);
        return false;
    } else {
        return true;
    }
}
function handleConnect($arg, $command, $pq=PRINT_QUERY)
{
    if ($command === "open") {
        $dbconn = pg_connect("host=localhost dbname=$arg user=nandeesh password=789&*(");
        if ($pq===true) {
            logInfo("connect- ", $dbconn);
        }
        if ($dbconn===false or pg_connection_status($dbconn)===PGSQL_CONNECTION_BAD) {
            logError("Couldn't connect to database!", $dbconn);
            return false;
        } else {
            return $dbconn;
        }
    } elseif ($command === "close") {
        if ($arg === false) {
            logError("No connection given", $arg);
            return false;
        }
        $res = pg_close($arg);
        if ($pq===true) {
            logInfo("closeconnec- ", $res);
        }
        if ($res === false) {
            logError("Cannot close connection", $res);
            return false;
        } else {
            return true;
        }
    } else {
        logError("Wrong arguments - ".$arg);
        return false;
    }
}
//--------------------------------------------------------------------------------------
