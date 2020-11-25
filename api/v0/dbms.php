<?php
require_once 'error.php';
require_once 'utility.php';

define("PRINT_QUERY", false);

function transaction($action, &$dbconn, $relation="codang")
{
    if (strcmp($action, 'begin')===0) {
        $dbconn = databaseConnection("open", $relation);
        if ($dbconn===false) {
            logError("cannot connect to db", $dbconn);
            return false;
        } else {
            logInfo("connected ", $dbconn);
        }
        $res = smallQuery('BEGIN', $dbconn);
        logInfo("BEGINNED", $res);
    } elseif (strcmp($action, 'rollback')===0) {
        if ($dbconn===false) {
            logError("cannot connect to db", $dbconn);
            return false;
        }
        $rb = smallQuery('ROLLBACK', $dbconn);
        $cl = databaseConnection("close", $relation, $dbconn);
        if ($rb===false or $cl===false) {
            return false;
        } else {
            logInfo("rolledback ", $dbconn);
        }
    } elseif (strcmp($action, 'commit')===0) {
        if ($dbconn===false) {
            logError("cannot connect to db", $dbconn);
            return false;
        }
        $rb = smallQuery('COMMIT', $dbconn);
        $cl = databaseConnection("close", $relation, $dbconn);
        if ($rb===false or $cl===false) {
            return false;
        } else {
            logInfo("commited and closed ", $dbconn);
        }
    } else {
        logError("Wrong action", $action);
        die();
    }
    return true;
}

function databaseConnection($arg, $relation="codang", $conn=null)
{
    if ($arg === "open") {
        $dbconn = pg_connect("host=localhost dbname=$relation user=nandeesh password=789&*(");
        if ($dbconn===false or pg_connection_status($dbconn)===PGSQL_CONNECTION_BAD) {
            logError("Couldn't connect to database!", $dbconn);
            return false;
        } else {
            return $dbconn;
        }
    } elseif ($arg === "close") {
        if ($conn === false) {
            logError("No connection given");
            return false;
        }
        $res = pg_close($conn);
        if ($res === false) {
            logError("Cannot close connection");
            return false;
        } else {
            return $res;
        }
    } else {
        logError("Wrong arguments - ".$arg);
        return false;
    }
}

//returns unformatted result
function smallQuery($query, $dbconn)
{
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
    } else {
        logError("db busy!", $dbconn);
    }
    $result = pg_get_result($dbconn);
    if ($result === false) {
        logError("Couldn't retrieve query result", $result);
        return false;
    }
    return $result;
}

//returns array of associated arrays
// use double loop to use the return value
// use json_encode(ret) to print
// see example at end of this file
function executeQuery($query, $dbconn, $print=false)
{
    $begin = smallQuery('BEGIN', $dbconn);
    if ($begin === false) {
        return false;
    }

    $result = smallQuery($query, $dbconn);

    if ($result === false) {
        smallQuery('ROLLBACK', $dbconn);
        return false;
    } else {
        $res_stat = pg_result_status($result);
        if ($res_stat==0 || $res_stat==5 || $res_stat==6 ||$res_stat==7) {
            $errstr = "SQL ERROR! - ".pg_result_error_field($result, PGSQL_DIAG_MESSAGE_PRIMARY).newline().pg_result_error_field($result, PGSQL_DIAG_MESSAGE_DETAIL).newline().pg_result_error_field($result, PGSQL_DIAG_MESSAGE_HINT).newline();
            logError($errstr, $result);
            smallQuery('ROLLBACK', $dbconn);
            return false;
        } else {
            $ret = array();
            $i=0;
            while ($row = pg_fetch_assoc($result, $i)) {
                $ret[] = $row;
                $i++;
            }
            if ($print) {
                $prstr = "QUERY - ".newline($query);
                foreach ($ret as $rowno => $row) {
                    foreach ($row as $key => $value) {
                        $prstr .= newline("{$key}-{$value}");
                    }
                }
                logInfo($prstr, $result);
            }
        }

        $commit = smallQuery('COMMIT', $dbconn);
        if ($commit === false) {
            return false;
        }
        return $ret;
    }
}

function nonTransactionQuery($query, $dbconn, $print=false)
{
    logInfo("in NTQ");
    $result = smallQuery($query, $dbconn);
    if ($result === false) {
        return false;
    } else {
        $res_stat = pg_result_status($result);
        if ($res_stat==0 || $res_stat==5 || $res_stat==6 ||$res_stat==7) {
            $errstr = "SQL ERROR! - ".pg_result_error_field($result, PGSQL_DIAG_MESSAGE_PRIMARY).newline().pg_result_error_field($result, PGSQL_DIAG_MESSAGE_DETAIL).newline().pg_result_error_field($result, PGSQL_DIAG_MESSAGE_HINT).newline();
            logError($errstr, $result);
            return false;
        } else {
            $ret = array();
            $i=0;
            while ($row = pg_fetch_assoc($result, $i)) {
                $ret[] = $row;
                $i++;
            }
            if ($print) {
                $prstr = "QUERY - ".newline($query);
                foreach ($ret as $rowno => $row) {
                    foreach ($row as $key => $value) {
                        $prstr .= newline("{$key}-{$value}");
                    }
                }
                logInfo($prstr, $result);
            }
        }
        return $ret;
    }
}

function connectAndExecuteQuery($query, $relation="codang")
{
    $dbconn = databaseConnection("open", $relation);
    $result = executeQuery($query, $dbconn, PRINT_QUERY);
    databaseConnection("close", $relation, $dbconn);
    return $result;
}

//example usage of return
//$ret = (connectAndExecuteQuery('select * from country'));
//foreach ($ret as $value) {
//    echo newline($value['name']).newline($value['code']).newline();
//    //foreach ($value as $k=>$v) {
//    //    echo $k."--".$v.newline();
//    //}
//}
//echo json_encode(connectAndExecuteQuery('select * from country'));
