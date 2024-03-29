<?php

require_once __DIR__ . "/CSVFile.php";
require_once __DIR__ . "/message-parsers.php";
require_once __DIR__ . "/utilities.php";

# Allow for debugging via HTTP or with CLI ("php index.php debug")
define("DEBUG", isset($_GET["debug"]) || (defined("STDIN") && array_search("debug", $_SERVER["argv"]) !== false));

if (DEBUG) {
    $message =
        #"(R) 12:17:42/63.872890,8.640409/b:136/c:98/s:0.00/sat:6/pp:24/cp:6/s:S/001011";
        #"(NAV) " . date("Y-m-d H:i:s") . "/63.8728 8.6404/C:89/dC:-20/r:-57/th:0/S:0.0/aws:1.7/awd:135";
        #"(ECO-P) " . date("Y-m-d H:i:s") . "/63.8729 8.6404/FDOM:393419008.0000/TU:0.0288/CHLA:0.1898";
        #"(CTD) " . date("Y-m-d H:i:s") . "/63.8728 8.6404/S:0.29/C:0.04/T:15.80/SS:-1.00/D:0.05";
        #"(OPT-P) " . date("Y-m-d H:i:s") . "/63.8728 8.6404/T:18.40/AS:98.87/DOX:289.87";
        #"(OPT-P) last " . date("Y-m-d H:i:s") . "/T:18.40/AS:98.87/DOX:289.87";
        #"(ADCP) " . date("Y-m-d H:i:s") . "/63.9020 8.6376/DP:4.500/S:0.221/D:-0.69";
        #"(ADCP) last " . date("Y-m-d H:i:s") . "/63.9020 8.6376/DP:4.500/S:0.221/D:-0.69";
        #"(A) Sheep is out of the fence!";
        "03290310,P, 63.44151 10.34867 41 1420";
    #"(R) 16:51:11/63.4394,10.4147/b:123/c:53/s:0.00/sat:21/pp:0/cp:33/t:0/s:S/001011";
} else {
    $json = getIridiumJSON();
    # {"iridium_session_status":0,"momsn":1065,"data":"2852292031313a32383a3334202f2036332035322e3337343337342c382033382e343239303831202f20623a31333620633a31313520733a302e3031207361743a352070703a31372063703a3132202f20733a53202f20303131303131","serial":16978,"iridium_latitude":63.8656,"iridium_cep":4.0,"JWT":"eyJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJSb2NrIDciLCJpYXQiOjE1OTI1NjYxNTMsImRhdGEiOiIyODUyMjkyMDMxMzEzYTMyMzgzYTMzMzQyMDJmMjAzNjMzMjAzNTMyMmUzMzM3MzQzMzM3MzQyYzM4MjAzMzM4MmUzNDMyMzkzMDM4MzEyMDJmMjA2MjNhMzEzMzM2MjA2MzNhMzEzMTM1MjA3MzNhMzAyZTMwMzEyMDczNjE3NDNhMzUyMDcwNzAzYTMxMzcyMDYzNzAzYTMxMzIyMDJmMjA3MzNhNTMyMDJmMjAzMDMxMzEzMDMxMzEiLCJkZXZpY2VfdHlwZSI6IlJPQ0tCTE9DSyIsImltZWkiOiIzMDAyMzQwNjg2Njk0NzAiLCJpcmlkaXVtX2NlcCI6IjQuMCIsImlyaWRpdW1fbGF0aXR1ZGUiOiI2My44NjU2IiwiaXJpZGl1bV9sb25naXR1ZGUiOiI4LjU3OTEiLCJpcmlkaXVtX3Nlc3Npb25fc3RhdHVzIjoiMCIsIm1vbXNuIjoiMTA2NSIsInNlcmlhbCI6IjE2OTc4IiwidHJhbnNtaXRfdGltZSI6IjIwLTA2LTE5IDExOjI5OjEwIn0.fints0W-LysFP5veMWxJeZNN4sScKbqpDJHfLsOzkst-cWMewkNlyt-sxrhyZNSyIgsmdj6sFMQvgHoz_oi8D2OLwlLGyjr2FUqOqvaR8RdSmqT-mUysGBilkCw5Y8TFW8x_BlEZNbxrVUWdCahpcOXjuP_VpYAyBM2m1w_1EbXI25XY5V2T5Fzhtkeavta3unp9Ay1iW5D_OdwVQ0lr437X7HKbkMTgoHz-7QJKt4kYktEC00UN2W9tyY-emiMH68AtwuR6OKvC-jDAK7PC7V9Cu_DtglTGQiuAsoG_7_M4eG59ZEnLb2HMGlN3HKtLcaTIV1Gn-Mqvd-pgcwg6BQ","imei":"300234068669470","device_type":"ROCKBLOCK","transmit_time":"20-06-19 11:29:10","iridium_longitude":8.5791}

    $data = getHexDataFromIridiumJSON($json);
    # 2852292031313a32383a3334202f2036332035322e3337343337342c382033382e343239303831202f20623a31333620633a31313520733a302e3031207361743a352070703a31372063703a3132202f20733a53202f20303131303131

    $message = hex2bin($data);
    $message = trim($message);
    #$message = "03260310,P, 63.44151 10.34867 41 1300";
    # Format: (R) 12:17:42/63.872890,8.640409/b:136/c:98/s:0.00/sat:6/pp:24/cp:6/s:S/001011
    # Format XEOS: 02231300,P, 63.44151 10.34867 40 1431
}

if (isset($message)){
    error_log("Message: '$message'");
}

//! Check if the received message is of (A) and trigger SMS notification to recipients.
if (isCOLAV($message) || strpos($message, "\x03\x88") === 0) {
    exit("Alert or COLAV message received, terminating ..");
}

if (isAlert($message)) {
    // sendSMS("+4791536919", $message);
    sendSMS("+4741308854", $message);
    return;
}


try {
    $csvFile = CSVFile::fromMessage($message);
} catch (Exception $ex) {
    error_log($ex->getMessage());
    exit($ex->getMessage());
}
$csvFilePath = $csvFile->getPath();
$table = getInfluxDBTableFromMessage($message);
$firstCSVLine = explode("\n", $csvFile->contents)[0];
$columns = explode(",", $firstCSVLine, 2)[1];
#$params = "--input {$csvFilePath} --user autonaut --password autonaut_influx --dbname AUTONAUT --metricname {$table} --fieldcolumns {$columns}";
$params = "--input {$csvFilePath} --dbname AUTONAUT_NEW --metricname {$table} --fieldcolumns {$columns}";

$csvFile->save();

$cmd = "python /home/autonaut/java_to_influx/csv-to.py {$params}";
#error_log($cmd);
# Execute the command in the shell
`{$cmd}`;
