<?php

include __DIR__ . '/../../vendor/autoload.php';

use Utils\ZillowClient;
use Elasticsearch\Client;

$client = new Client(['hosts' => ['http://localhost:9200']]);

$zClient = new ZillowClient();
$results = json_decode($zClient->getDataForAllZips());

$params = [];
$params['index'] = 'hrqls';
$params['type'] = 'houseData';
foreach ($results as $item) {
    $params['body'][] = array(
        'create' => array(
            '_id' => sha1($item->location->lat . $item->location->lon)
          )
    );
    $params['body'][] = array(
        'state' => $item->state,
        'city' => $item->city,
        'zip' => $item->zip,
        'location' => array(
            'lat' => $item->location->lat,
            'lon' => $item->location->lon
        ),
        'avgHomeValueIndex' => $item->homeData->avgHomeValueIndex,
        'avgHomesRecentlySold' => $item->homeData->avgHomesRecentlySold,
        'avgPropertyTax' => $item->homeData->avgPropertyTax,
        'turnoverWithinLastYear' => $item->homeData->turnoverWithinLastYear
    );
}

$client->bulk($params);
