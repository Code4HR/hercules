<?php

include __DIR__ . '/../../vendor/autoload.php';

$bootstrap = new HRQLS\Bootstrap(new Silex\Application());

$bootstrap->loadConfig();

$bootstrap->connectDatabases();

$bootstrap->startupSite();

$webClient = new GuzzleHttp\Client();
$consumer = new CrimeClient($webClient);

foreach($bootstrap->config['cities'] as $city)
{
    $toDate = new \DateTime();
    $fromDate = new \DateTime('-7 days');
    list($toYear, $toMonth, $toDay) = explode('-', $toDate->format('Y-m-d'));
    list($fromYear, $fromMonth, $fromDay) = explode('-', $fromDate->format('Y-m-d'));
    $cityUrl = str_replace('{city}', $city, $bootstrap->config['crimeUrl']);
    $cityUrl = str_replace('{toYear}', $toYear, $cityUrl);
    $cityUrl = str_replace('{toMonth}', $toMonth, $cityUrl);
    $cityUrl = str_replace('{toDay}', $toDay, $cityUrl);
    $cityUrl = str_replace('{fromYear}', $fromYear, $cityUrl);
    $cityUrl = str_replace('{fromMonth}', $fromMonth, $cityUrl);
    $cityUrl = str_replace('{fromDay}', $fromDay, $cityUrl);

    try{
        $consumer->consume($city, $cityUrl);
    }
    catch(Exception $e)
    {
        echo "Failed to pull crime data for {$city}";
    }
    //@TODO  Update record in ES to show city has current crime data
}
