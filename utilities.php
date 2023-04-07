<?php

function checkAlert(string $message)
{
    $message_type = preg_replace('/^\((\w+).*$/', '$1', $message);

    if ($message_type && $message_type == "A") {
        $cmd = "curl -X POST https://textbelt.com/text \
        --data-urlencode phone='+4741308854' \
        --data-urlencode message='{$message}' \
        -d key=20e7455c";
        #error_log($cmd);
        # Execute the command in the shell
	// `{$cmd}`;


        $cmd = "curl -X POST https://textbelt.com/text \
        --data-urlencode phone='+4791536919' \
        --data-urlencode message='{$message}' \
        -d key=20e7455c";
        #error_log($cmd);
        # Execute the command in the shell
        // `{$cmd}`;

        #$cmd = "curl -X POST https://textbelt.com/text \
        #--data-urlencode phone='+4790945805' \
        #--data-urlencode message='{$message}' \
        #-d key=20e7455c";
        ##error_log($cmd);
        # Execute the command in the shell
        // `{$cmd}`;

        return true;
    } else
    {
	return false;
    }
}

function checkCOLAV(string $message): bool
{
	return false !== strpos($message,"COLAV");
}

function getIridiumJSON(): string
{
    return file_get_contents('php://input');
}

function getHexDataFromIridiumJSON(string $json): string
{
    $ret = json_decode($json, true)["data"];
    if ($ret === NULL){
        error_log("could onot convert this to json: " . $json);
        header("HTTP/1.1 500 Internal Server Error");
        exit(1);
    }
    return $ret;
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
