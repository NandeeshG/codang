<?php

require_once 'utility.php';

function logError($errstr, $resource=null)
{
    echo newline()."------XXXXXXXXX------".newline();
    debug_print_backtrace();
    echo newline()."ERROR ".date('H:i:s').": ".$errstr.newline();
    echo "RESOURCE: ".json_encode($resource).newline();
    var_dump($resource);
    echo newline()."------XXXXXXXXX------".newline();
    echo newline();
}

function logInfo($infostr, $resource=null)
{
    echo newline()."------KKKKKKKKK------".newline();
    debug_print_backtrace();
    echo newline()."INFO ".date('H:i:s').": ".$infostr.newline();
    echo "RESOURCE: ".json_encode($resource).newline();
    var_dump($resource);
    echo newline()."------KKKKKKKKK------".newline();
    echo newline();
}
