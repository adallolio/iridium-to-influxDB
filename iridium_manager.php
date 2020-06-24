<?php

if (isset($_GET["debug"])) {
    $message =
    #"(R) 12:17:42/63.872890,8.640409/b:136/c:98/s:0.00/sat:6/pp:24/cp:6/s:S/001011";
    #"(NAV) 2020-06-23 12:25:21/63.8728 8.6404/C:89/dC:-20/r:-57/th:0/S:0.0/aws:1.7/awd:135";
    #"(ECO-P) 2020-06-22 09:05:42/63.8729 8.6404/FDOM:393419008.0000/TU:0.0288/CHLA:0.1898";
    #"(CTD) 2020-06-23 09:37:49/63.8728 8.6404/S:0.29/C:0.04/T:15.80/SS:-1.00/D:0.05";
    "(OPT-P) 2020-06-23 08:52:44/63.8728 8.6404/T:18.40/AS:98.87/DOX:289.87";
    #"TBL";
    #"ADCP";
} else {
    $json = getIridiumJSON();
    # {"iridium_session_status":0,"momsn":1065,"data":"2852292031313a32383a3334202f2036332035322e3337343337342c382033382e343239303831202f20623a31333620633a31313520733a302e3031207361743a352070703a31372063703a3132202f20733a53202f20303131303131","serial":16978,"iridium_latitude":63.8656,"iridium_cep":4.0,"JWT":"eyJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJSb2NrIDciLCJpYXQiOjE1OTI1NjYxNTMsImRhdGEiOiIyODUyMjkyMDMxMzEzYTMyMzgzYTMzMzQyMDJmMjAzNjMzMjAzNTMyMmUzMzM3MzQzMzM3MzQyYzM4MjAzMzM4MmUzNDMyMzkzMDM4MzEyMDJmMjA2MjNhMzEzMzM2MjA2MzNhMzEzMTM1MjA3MzNhMzAyZTMwMzEyMDczNjE3NDNhMzUyMDcwNzAzYTMxMzcyMDYzNzAzYTMxMzIyMDJmMjA3MzNhNTMyMDJmMjAzMDMxMzEzMDMxMzEiLCJkZXZpY2VfdHlwZSI6IlJPQ0tCTE9DSyIsImltZWkiOiIzMDAyMzQwNjg2Njk0NzAiLCJpcmlkaXVtX2NlcCI6IjQuMCIsImlyaWRpdW1fbGF0aXR1ZGUiOiI2My44NjU2IiwiaXJpZGl1bV9sb25naXR1ZGUiOiI4LjU3OTEiLCJpcmlkaXVtX3Nlc3Npb25fc3RhdHVzIjoiMCIsIm1vbXNuIjoiMTA2NSIsInNlcmlhbCI6IjE2OTc4IiwidHJhbnNtaXRfdGltZSI6IjIwLTA2LTE5IDExOjI5OjEwIn0.fints0W-LysFP5veMWxJeZNN4sScKbqpDJHfLsOzkst-cWMewkNlyt-sxrhyZNSyIgsmdj6sFMQvgHoz_oi8D2OLwlLGyjr2FUqOqvaR8RdSmqT-mUysGBilkCw5Y8TFW8x_BlEZNbxrVUWdCahpcOXjuP_VpYAyBM2m1w_1EbXI25XY5V2T5Fzhtkeavta3unp9Ay1iW5D_OdwVQ0lr437X7HKbkMTgoHz-7QJKt4kYktEC00UN2W9tyY-emiMH68AtwuR6OKvC-jDAK7PC7V9Cu_DtglTGQiuAsoG_7_M4eG59ZEnLb2HMGlN3HKtLcaTIV1Gn-Mqvd-pgcwg6BQ","imei":"300234068669470","device_type":"ROCKBLOCK","transmit_time":"20-06-19 11:29:10","iridium_longitude":8.5791}

    $data = getHexDataFromIridiumJSON($json);
    # 2852292031313a32383a3334202f2036332035322e3337343337342c382033382e343239303831202f20623a31333620633a31313520733a302e3031207361743a352070703a31372063703a3132202f20733a53202f20303131303131

    $message = getMessageFromHexData($data);
    # Format: (R) 12:17:42/63.872890,8.640409/b:136/c:98/s:0.00/sat:6/pp:24/cp:6/s:S/001011
}

#isset($json) && error_log("JSON:         {$json}");
#isset($data) && error_log("Hex data:     {$data}");
isset($message) && error_log("Message:      {$message}");

try {
    $csvFile = CSVFile::fromMessage($message);
} catch (Exception $ex) {
    http_response_code(400);
    error_log($ex->getMessage());
    exit($ex->getMessage());
}
$csvFilePath = $csvFile->getPath();
$table = getInfluxDBTableFromMessage($message);
$firstCSVLine = explode("\n", $csvFile->contents)[0];
$columns = explode(",", $firstCSVLine, 2)[1];
$params = "--input {$csvFilePath} --user autonaut --password ntnu_autonaut --dbname AUTONAUT --metricname {$table} --fieldcolumns {$columns}";

$csvFile->save();

$cmd = "python /home/autonaut/java_to_influx/csv-to.py {$params}";
#error_log($cmd);
`{$cmd}`;

#####################
## Functions
#####################

function getIridiumJSON(): string
{
    return file_get_contents('php://input');
}

function getHexDataFromIridiumJSON(string $json): string
{
    return json_decode($json, true)["data"];
}

function getMessageFromHexData(string $data): string
{
    $numbersOfCharsInData = strlen($data) / 2;

    $message = "";
    for ($i = 0; $i < $numbersOfCharsInData; $i++) {
        $byteInHex = $data[2 * $i] . $data[2 * $i + 1];

        list($byte) = sscanf($byteInHex, "%2x");

        $message .= chr($byte);
    }

    return $message;
}

/**
 * Drop integer indexed elements of the given matches array
 * e.g. [0=>"a", "letter"=>"a"] => ["letter"=>"a"]
 */
function getNamedCapturesFromMatches(array $matches): array
{
    foreach ($matches as $key => $value) {
        if (preg_match('/^\d+$/', $key)) {
            unset($matches[$key]);
        }
    }
    return $matches;
}

/**
 * Make a suitable table name, e.g. "(ECO-P) …" => "eco_iridium"
 */
function getInfluxDBTableFromMessage(string $message): string
{
    $messageType = getMessageTypeFromMessage($message);
    return strtolower($messageType) . "_iridium";
}

/**
 * Extract message type from message, e.g. "(ECO-P) …" => "ECO"
 */
function getMessageTypeFromMessage(string $message): string
{
    return preg_replace('/^\((\w+).*$/', '$1', $message);
}

#####################
## Message handlers
#####################

function handlePeriodicalReport(string $message): CSVFile
{
    $matches = [];
    preg_match('_^'
        . '\((?<type>\w+)[^)]*\) '
        . '(?<timestamp>[^/]{8})/'
        . '(?<lat>[-\d. ]+),(?<lon>[-\d. ]+)/'
        . 'b:(?<b>[-\d.]+)/'
        . 'c:(?<c>[-\d.]+)/'
        . 's:(?<s>[-\d.]+)/'
        . 'sat:(?<sat>[-\d.]+)/'
        . 'pp:(?<pp>[-\d.]+)/'
        . 'cp:(?<cp>[-\d.]+)/'
        . 's:(?<status>\w)/'
        . '(?<relays>[01]+)'
        . '$_', $message, $matches);

    $matches["timestamp"] = date("Y-m-d ") . $matches["timestamp"];

    return CSVFile::fromMatches($matches);
}

function handleNavigationStatus($message): CSVFile
{
    $matches = [];
    preg_match('_^'
        . '\((?<type>\w+).*\) '
        . '(?<timestamp>\d{4}-\d{2}-\d{2} [^/]+)/'
        . '(?<lat>[-\d.]+) (?<lon>[-\d.]+)/'
        . 'C:(?<C>[-\d.]+)/'
        . 'dC:(?<dC>[-\d.]+)/'
        . 'r:(?<r>[-\d.]+)/'
        . 'th:(?<th>[-\d.]+)/'
        . 'S:(?<S>[-\d.]+)/'
        . 'aws:(?<aws>[-\d.]+)/'
        . 'awd:(?<awd>[-\d.]+)'
        . '$_', $message, $matches);

    $matches = getNamedCapturesFromMatches($matches);
    return CSVFile::fromMatches($matches);
}

function handleCTD(string $message): CSVFile
{
    $matches = [];
    preg_match('_^'
        . '\((?<type>\w+)[^)]*\) '
        . '(?<timestamp>\d{4}-\d{2}-\d{2} [^/]+)/'
        . '(?<lat>[-\d.]+) (?<lon>[-\d.]+)/'
        . 'S:(?<S>[-\d.]+)/'
        . 'C:(?<C>[-\d.]+)/'
        . 'T:(?<T>[-\d.]+)/'
        . 'SS:(?<SS>[-\d.]+)/'
        . 'D:(?<D>[-\d.]+)'
        . '$_', $message, $matches);

    $matches = getNamedCapturesFromMatches($matches);
    return CSVFile::fromMatches($matches);
}

function handleECO(string $message): CSVFile
{
    $matches = [];
    preg_match('_^'
        . '\((?<type>\w+)[^)]*\) '
        . '(?<timestamp>\d{4}-\d{2}-\d{2} [^/]+)/'
        . '(?<lat>[-\d.]+) (?<lon>[-\d.]+)/'
        . 'FDOM:(?<FDOM>[-\d.]+)/'
        . 'TU:(?<TU>[-\d.]+)/'
        . 'CHLA:(?<CHLA>[-\d.]+)'
        . '$_', $message, $matches);

    $matches = getNamedCapturesFromMatches($matches);
    return CSVFile::fromMatches($matches);
}

function handleOPT(string $message): CSVFile
{
    #(OPT) 2020/06/23 08:51:44/63.8728 8.6404/T:18.40/AS:98.87/DOX:289.87
    $matches = [];
    preg_match('_^'
        . '\((?<type>\w+)[^)]*\) '
        . '(?<timestamp>\d{4}-\d{2}-\d{2} [^/]+)/'
        . '(?<lat>[-\d.]+) (?<lon>[-\d.]+)/'
        . 'T:(?<T>[-\d.]+)/'
        . 'AS:(?<AS>[-\d.]+)/'
        . 'DOX:(?<DOX>[-\d.]+)'
        . '$_', $message, $matches);

    $matches = getNamedCapturesFromMatches($matches);
    return CSVFile::fromMatches($matches);
}

function handleTBL(string $message): CSVFile
{
    #
    $matches = [];
    preg_match('_^'
        . '\((?<type>\w+)[^)]*\) '
        . '(?<timestamp>\d{4}-\d{2}-\d{2} [^/]+)/'
        . '(?<lat>[-\d.]+) (?<lon>[-\d.]+)/'
        . 'SN:(?<SN>[-\d]+)/'
        . 'T:(?<T>[-\d.]+)/'
        . 'ANL:(?<ANL>[-\d]+)/'
        . 'PNL:(?<PNL>[-\d]+)/'
        . 'RLF:(?<RLF>[-\d]+)/'
        . 'RMA:(?<RMA>[-\d]+)'
        . '$_', $message, $matches);

    $matches = getNamedCapturesFromMatches($matches);
    return CSVFile::fromMatches($matches);
}

function handleADCP(string $message): CSVFile
{
    ;
}

#####################
## Classes
#####################

class CSVFile
{
    public $filename;
    public $contents;

    public static $csvDir = "/home/autonaut/java_to_influx/iridium";

    /**
     * @param string $filename filename, excluding directories
     *                         (e.g. foo.csv)
     * @param string $contents text content to store in the CSV file
     *                         (e.g. id,name\n1,NTNU)
     */
    public function __construct(string $filename, string $contents)
    {
        $this->filename = $filename;
        $this->contents = $contents;
    }

    /**
     * Write contents to disk
     */
    public function save()
    {
        file_put_contents($this->getPath(), "{$this->contents}\n");
    }

    /**
     * Get full path to the CSV file (e.g. /home/bar/foo.csv).
     */
    public function getPath(): string
    {
        return self::$csvDir . "/" . $this->filename;
    }

    /**
     * Construct CSV from assoc. array using non-number keys as columns and
     * values for the first row of data.
     */
    public static function fromMatches(array $matches): CSVFile
    {
        $type = $matches["type"];
        unset($matches["type"]);

        $matches = getNamedCapturesFromMatches($matches);

        $csv =
        implode(",", array_keys($matches))
        . "\n"
        . implode(",", $matches);

        isset($_GET["debug"]) && error_log($csv);

        return new CSVFile("{$type}.csv", $csv);
    }

    public static function fromMessage(string $message): CSVFile
    {
        $messageType = getMessageTypeFromMessage($message);

        switch ($messageType) {
            case "R":
                return handlePeriodicalReport($message);

            case "NAV":
                return handleNavigationStatus($message);

            case "CTD":
                return handleCTD($message);

            case "ECO":
                return handleECO($message);

            case "OPT":
                return handleOPT($message);

            case "TBL":
                return handleTBL($message);

            case "ADCP":
                return handleADCP($message);

            default:
                throw new Exception("Unsupported messageType '{$messageType}'");
        }
    }
}
