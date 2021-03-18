<?php

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

        $last = $matches["last"];
        unset($matches["last"]);
        if ($last) {
            $matches["lat"] = "0.0";
            $matches["lon"] = "0.0";
        }

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

            case "PAR":
                return handlePAR($message);

            case "RAD":
                return handleRadiation($message);

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
