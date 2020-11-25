<?php
function mchk($var, $str = "VAR")
{
    if ($var) {
        echo "<hr/> OK: {$str} <hr/>";
    } else {
        die("<hr/> ERROR: {$str} <hr/>");
    }
}

$ch = curl_init();
mchk($ch, "init");
$url = curl_setopt($ch, CURLOPT_URL, "https://api.codechef.com/contests/PRACTICE/problems/SALARY");
mchk($url, "opt");
$hdr = array("Accept: application/json", "Authorization: Bearer 7591812c00df1550bcb5bf4769f883bea5ee3aa7");
$sethdr = curl_setopt($ch, CURLOPT_HTTPHEADER, $hdr);
mchk($sethdr, "set hdr");

//$hdrout = curl_setopt($ch, CURLINFO_HEADER_OUT, true);
//$info = curl_getinfo($ch);
//foreach ($info as $key => $value) {
//    echo $key . "=>" . $value . "<br/>";
//}

$data = curl_exec($ch);
mchk($data, "exec");
curl_close($ch);
mchk(true, "closed");

echo html_entity_decode($data['result']['data']['content']['body']);
