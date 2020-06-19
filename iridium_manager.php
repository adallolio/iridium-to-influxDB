<?php

$json = file_get_contents('php://input');
# {"iridium_session_status":0,"momsn":1065,"data":"2852292031313a32383a3334202f2036332035322e3337343337342c382033382e343239303831202f20623a31333620633a31313520733a302e3031207361743a352070703a31372063703a3132202f20733a53202f20303131303131","serial":16978,"iridium_latitude":63.8656,"iridium_cep":4.0,"JWT":"eyJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJSb2NrIDciLCJpYXQiOjE1OTI1NjYxNTMsImRhdGEiOiIyODUyMjkyMDMxMzEzYTMyMzgzYTMzMzQyMDJmMjAzNjMzMjAzNTMyMmUzMzM3MzQzMzM3MzQyYzM4MjAzMzM4MmUzNDMyMzkzMDM4MzEyMDJmMjA2MjNhMzEzMzM2MjA2MzNhMzEzMTM1MjA3MzNhMzAyZTMwMzEyMDczNjE3NDNhMzUyMDcwNzAzYTMxMzcyMDYzNzAzYTMxMzIyMDJmMjA3MzNhNTMyMDJmMjAzMDMxMzEzMDMxMzEiLCJkZXZpY2VfdHlwZSI6IlJPQ0tCTE9DSyIsImltZWkiOiIzMDAyMzQwNjg2Njk0NzAiLCJpcmlkaXVtX2NlcCI6IjQuMCIsImlyaWRpdW1fbGF0aXR1ZGUiOiI2My44NjU2IiwiaXJpZGl1bV9sb25naXR1ZGUiOiI4LjU3OTEiLCJpcmlkaXVtX3Nlc3Npb25fc3RhdHVzIjoiMCIsIm1vbXNuIjoiMTA2NSIsInNlcmlhbCI6IjE2OTc4IiwidHJhbnNtaXRfdGltZSI6IjIwLTA2LTE5IDExOjI5OjEwIn0.fints0W-LysFP5veMWxJeZNN4sScKbqpDJHfLsOzkst-cWMewkNlyt-sxrhyZNSyIgsmdj6sFMQvgHoz_oi8D2OLwlLGyjr2FUqOqvaR8RdSmqT-mUysGBilkCw5Y8TFW8x_BlEZNbxrVUWdCahpcOXjuP_VpYAyBM2m1w_1EbXI25XY5V2T5Fzhtkeavta3unp9Ay1iW5D_OdwVQ0lr437X7HKbkMTgoHz-7QJKt4kYktEC00UN2W9tyY-emiMH68AtwuR6OKvC-jDAK7PC7V9Cu_DtglTGQiuAsoG_7_M4eG59ZEnLb2HMGlN3HKtLcaTIV1Gn-Mqvd-pgcwg6BQ","imei":"300234068669470","device_type":"ROCKBLOCK","transmit_time":"20-06-19 11:29:10","iridium_longitude":8.5791}

##
isset($_GET["debug"]) && $json = '{"iridium_session_status":0,"momsn":1065,"data":"2852292031313a32383a3334202f2036332035322e3337343337342c382033382e343239303831202f20623a31333620633a31313520733a302e3031207361743a352070703a31372063703a3132202f20733a53202f20303131303131","serial":16978,"iridium_latitude":63.8656,"iridium_cep":4.0,"JWT":"eyJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJSb2NrIDciLCJpYXQiOjE1OTI1NjYxNTMsImRhdGEiOiIyODUyMjkyMDMxMzEzYTMyMzgzYTMzMzQyMDJmMjAzNjMzMjAzNTMyMmUzMzM3MzQzMzM3MzQyYzM4MjAzMzM4MmUzNDMyMzkzMDM4MzEyMDJmMjA2MjNhMzEzMzM2MjA2MzNhMzEzMTM1MjA3MzNhMzAyZTMwMzEyMDczNjE3NDNhMzUyMDcwNzAzYTMxMzcyMDYzNzAzYTMxMzIyMDJmMjA3MzNhNTMyMDJmMjAzMDMxMzEzMDMxMzEiLCJkZXZpY2VfdHlwZSI6IlJPQ0tCTE9DSyIsImltZWkiOiIzMDAyMzQwNjg2Njk0NzAiLCJpcmlkaXVtX2NlcCI6IjQuMCIsImlyaWRpdW1fbGF0aXR1ZGUiOiI2My44NjU2IiwiaXJpZGl1bV9sb25naXR1ZGUiOiI4LjU3OTEiLCJpcmlkaXVtX3Nlc3Npb25fc3RhdHVzIjoiMCIsIm1vbXNuIjoiMTA2NSIsInNlcmlhbCI6IjE2OTc4IiwidHJhbnNtaXRfdGltZSI6IjIwLTA2LTE5IDExOjI5OjEwIn0.fints0W-LysFP5veMWxJeZNN4sScKbqpDJHfLsOzkst-cWMewkNlyt-sxrhyZNSyIgsmdj6sFMQvgHoz_oi8D2OLwlLGyjr2FUqOqvaR8RdSmqT-mUysGBilkCw5Y8TFW8x_BlEZNbxrVUWdCahpcOXjuP_VpYAyBM2m1w_1EbXI25XY5V2T5Fzhtkeavta3unp9Ay1iW5D_OdwVQ0lr437X7HKbkMTgoHz-7QJKt4kYktEC00UN2W9tyY-emiMH68AtwuR6OKvC-jDAK7PC7V9Cu_DtglTGQiuAsoG_7_M4eG59ZEnLb2HMGlN3HKtLcaTIV1Gn-Mqvd-pgcwg6BQ","imei":"300234068669470","device_type":"ROCKBLOCK","transmit_time":"20-06-19 11:29:10","iridium_longitude":8.5791}';
##

$data = json_decode($json, true)["data"];
# 2852292031313a32383a3334202f2036332035322e3337343337342c382033382e343239303831202f20623a31333620633a31313520733a302e3031207361743a352070703a31372063703a3132202f20733a53202f20303131303131

$numbersOfCharsInData = strlen($data) / 2;

$message = "";
for ($i = 0; $i < $numbersOfCharsInData; $i++) {
    $byteInHex = $data[2 * $i] . $data[2 * $i + 1];

    list($byte) = sscanf($byteInHex, "%2x");

    $message .= chr($byte);
}
# Format: (R) 12:17:42/63.872890,8.640409/b:136/c:98/s:0.00/sat:6/pp:24/cp:6/s:S/001011

##
isset($_GET["debug"]) && $message = "(R) 12:17:42/63.872890,8.640409/b:136/c:98/s:0.00/sat:6/pp:24/cp:6/s:S/001011";
##

error_log("Message: {$message}");

$messageType = preg_replace('/^\((\w+).*$/', '$1', $message);

switch ($messageType) {
    case "R":
        handleReport($message);
        break;

    default:
        http_response_code(400);
        break;
}

function handleReport(string $message)
{
    $matches = [];
    preg_match('_^'
        . '\((?<type>\w+)\) '
        . '(?<timestamp>[^/]{8})/'
        . '(?<lat>[\d. ]+),(?<lon>[\d. ]+)/'
        . 'b:(?<b>\d+)/'
        . 'c:(?<c>[-\d]+)/'
        . 's:(?<s>[\d.]+)/'
        . 'sat:(?<sat>\d+)/'
        . 'pp:(?<pp>\d+)/'
        . 'cp:(?<cp>\d+)/'
        . 's:(?<status>\w)/'
        . '(?<relays>[01]+)'
        . '$_', $message, $matches);

    $timestamp = "";

    $csv =
    implode(",",
        [
            "timestamp",
            "lat",
            "lon",
            "b",
            "c",
            "s",
            "sat",
            "pp",
            "cp",

            "status",
            "relays",
        ])
    . "\n"
    . implode(",",
        [
            date("Y-m-d ") . $matches["timestamp"],
            $matches["lat"],
            $matches["lon"],
            $matches["b"],
            $matches["c"],
            $matches["s"],
            $matches["sat"],
            $matches["pp"],
            $matches["cp"],
            $matches["status"],
            $matches["relays"],
        ])
        . "\n";

    echo $csv;
    file_put_contents("/home/autonaut/java_to_influx/iridium/report.csv", $csv);

    #`./csv-to.py /home/autonaut/java_to_influx/iridium/report.csv $bunchOfStuff`;
}
