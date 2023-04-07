<?php

function isAlert(string $message): bool
{
    $message_type = preg_replace('/^\((\w+).*$/', '$1', $message);
    return $message_type === "A";
}

function sendSMS(string $phoneNumber, string $message): void
{
    $cmd = "curl -X POST https://textbelt.com/text";
    $cmd .= "--data-urlencode phone='$phoneNumber'";
    $cmd .= "--data-urlencode message='$message'";
    $cmd .= "-d key=20e7455c";

    `$cmd`;
}

function isCOLAV(string $message): bool
{
    return 0 === strpos($message, "COLAV");
}

function getIridiumJSON(): string
{
    return file_get_contents('php://input');
}

function getHexDataFromIridiumJSON(string $json): string
{
    $ret = json_decode($json, true);
    if ($ret === NULL) {
        error_log("could onot convert this to json: " . $json);
        header("HTTP/1.1 500 Internal Server Error");
        exit(1);
    }
    return $ret["data"];
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
    $message_types = [
        "R",
        "NAV",
        "CTD",
        "ECO",
        "PAR",
        "RAD",
        "OPT",
        "TBL",
        "ADCP",
        "A"
    ];

    $message_type = getMessageTypeFromMessage($message);
    if (in_array($message_type, $message_types, true)) {
        return strtolower($message_type) . "_iridium";
    }
    return "xeos";
}

/**
 * Extract message type from message, e.g. "(ECO-P) …" => "ECO"
 */
function getMessageTypeFromMessage(string $message): string
{
    return preg_replace('/^\((\w+).*$/', '$1', $message);
}
