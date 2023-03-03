<?php

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
    if($messageType!="R" && $messageType!="NAV" && $messageType!="CTD" && $messageType!="ECO" && $messageType!="PAR" && $messageType!="RAD" && $messageType!="OPT" && $messageType!="TBL" && $messageType!="ADCP" && $messageType!="A")
        return "xeos";
    else
        return strtolower($messageType) . "_iridium";
}

/**
 * Extract message type from message, e.g. "(ECO-P) …" => "ECO"
 */
function getMessageTypeFromMessage(string $message): string
{
    return preg_replace('/^\((\w+).*$/', '$1', $message);
}
