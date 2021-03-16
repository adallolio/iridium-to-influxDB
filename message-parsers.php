<?php

function handlePeriodicalReport(string $message): CSVFile
{
    $matches = [];
    preg_match('_^'
        . '\((?<type>\w+)[^)]*\) '
        . '((?<last>last) )?'
        . '(?<timestamp>[^/]{8})/'
        . '((?<lat>[-\d. ]+),(?<)lon>[-\d. ]+)/?'
        . 'b:(?<b>[-\d.]+)/'
        . 'c:(?<c>[-\d.]+)/'
        . 's:(?<s>[-\d.]+)/'
        . 'sat:(?<sat>[-\d.]+)/'
        . 'pp:(?<pp>[-\d.]+)/'
        . 'cp:(?<cp>[-\d.]+)/'
        . 't:(?<t>[-\d.]+)/'
        . 's:(?<status>\w)/'
        . '(?<l2>[01])'
        . '(?<l3>[01])'
        . '(?<iridium>[01])'
        . '(?<modem>[01])'
        . '(?<pumps>[01])'
        . '(?<vhf>[01])'
        . '$_', $message, $matches);

    $matches["timestamp"] = date("Y-m-d ") . $matches["timestamp"];

    return CSVFile::fromMatches($matches);
}

function handleNavigationStatus($message): CSVFile
{
    $matches = [];
    preg_match('_^'
        . '\((?<type>\w+).*\) '
        . '((?<last>last) )?'
        . '(?<timestamp>\d{4}-\d{2}-\d{2} [^/]+)/'
        . '((?<lat>[-\d.]+) (?<lon>[-\d.]+)/)?'
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
        . '((?<last>last) )?'
        . '(?<timestamp>\d{4}-\d{2}-\d{2} [^/]+)/'
        . '((?<lat>[-\d.]+) (?<lon>[-\d.]+)/)?'
        . 'S:(?<S>[-\d.]+)/'
        . 'C:(?<C>[-\d.]+)/'
        . 'T:(?<T>[-\d.]+)/'
        . 'SS:(?<SS>[-\d.]+)/'
        . 'D:(?<D>[-\d.]+)/'
        . 'P:(?<P>[-\d.]+)'
        . '$_', $message, $matches);

    $matches = getNamedCapturesFromMatches($matches);
    return CSVFile::fromMatches($matches);
}

function handleECO(string $message): CSVFile
{
    $matches = [];
    preg_match('_^'
        . '\((?<type>\w+)[^)]*\) '
        . '((?<last>last) )?'
        . '(?<timestamp>\d{4}-\d{2}-\d{2} [^/]+)/'
        . '((?<lat>[-\d.]+) (?<lon>[-\d.]+)/)?'
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
        . '((?<last>last) )?'
        . '(?<timestamp>\d{4}-\d{2}-\d{2} [^/]+)/'
        . '((?<lat>[-\d.]+) (?<lon>[-\d.]+)/)?'
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
        . '((?<last>last) )?'
        . '(?<timestamp>\d{4}-\d{2}-\d{2} [^/]+)/'
        . '((?<lat>[-\d.]+) (?<lon>[-\d.]+)/)?'
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
    # (ADCP) 2021-03-11 22:28:59/63.9020 8.6376/DP:4.500/S:0.221/D:-0.69
    # depth (DP), speed (S) and dir (D)
    $matches = [];
    preg_match('_^'
        . '\((?<type>\w+)[^)]*\) '
        . '((?<last>last) )?'
        . '(?<timestamp>\d{4}-\d{2}-\d{2} [^/]+)/'
        . '((?<lat>[-\d.]+) (?<lon>[-\d.]+)/)?'
        . 'DP:(?<depth>[-\d.]+)/'
        . 'S:(?<speed>[-\d.]+)/'
        . 'D:(?<dir>[-\d.]+)'
        . '$_', $message, $matches);

    $matches = getNamedCapturesFromMatches($matches);
    return CSVFile::fromMatches($matches);
}
