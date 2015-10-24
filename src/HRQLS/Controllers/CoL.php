<?php
namespace HRQLS\Controllers;

use Silex\Application as Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Elasticsearch\Client;

class CoL
{
    public function main(Request $req, Application $app)
    {
        $client = new Client();

        $params = array(
             'index'  => 'hrqls',
             'type'   => 'houseData',
             'body' => [
                 'from' => 0,
                 'size' => 100,
                 'filter' => array(
                    'range' => array(
                        'avgHomeValueIndex' => array(
                            'gte' => 0
                        )
                    )
                 ),
                 'query' => [
                     'match' => [
                         'state' => 'Virginia'
                         ]
                     ]
                 ]
        );

        $results = $client->search($params)['hits']['hits'];
        $responseObject = [];
        $averageHouseValue = array(
            'total' => 0,
            'number' => 0
          );
        $averageTurnover = array(
            'total' => 0,
            'number' => 0
        );
        $maxHouseValue = 0;
        $minHouseValue = 900000;

        foreach ($results as $zip) {
            $averageHouseValue['total'] += $zip['_source']['avgHomeValueIndex'];
            $averageHouseValue['number']++;

            $averageTurnover['total'] += $zip['_source']['turnoverWithinLastYear'];
            $averageTurnover['number']++;

            if ($zip['_source']['avgHomeValueIndex'] > $maxHouseValue) {
                $maxHouseValue = $zip['_source']['averageHouseValue'];
            }

            if ($zip['_source']['averageHouseValue'] < $minHouseValue) {
                $minHouseValue = $zip['_source']['averageHouseValue'];
            }
        }


        $averageHouse = $averageHouseValue['total'] / $averageHouseValue['number'];
        $averageTurn = $averageTurnover['total'] / $averageTurnover['number'];
        $slidervalue = $req->get('slidervalue');
        foreach ($results as $zip) {
          $sliderInfo = $this->calculate($slider);
          $weight = $this->determineWeight($sliderInfo, $zip['_source']['avgHomeValueIndex'], $averageHouse, $maxHouseValue, $minHouseValue);
          $responseObject[] = array(
              'lat' => $zip['_source']['location']['lat'],
              'lon' => $zip['_source']['location']['lon'],
              'weight' => $weight
            );
        }

        return new Response(json_encode($responseObject), 200);
    }

    private function calculate($slider)
    {
        $direction = false;
        $distance = 0;
        if ($slider >= 50) {
            $direction = true;
            $distance = ((($slider - 50) * 1) + 50) / 50;
        } else {
            $distance = $slider / 50;
        }

        return array('direction' => $direction, 'distance' => $distance);
    }

    private function determineWeight($sliderInfo, $currentValue, $average, $max, $min)
    {
        $targetValue = 0;
        $scale = 0;
        if ($sliderInfo['direction']) {
            $targetValue = $sliderInfo['distance'] * ($max - $average);
            $scale = $targetValue - $min;
        } else {
            $targetValue = (1 - $sliderInfo['distance']) * ($average - $min);
            $scale = $max - $targetValue;
        }

        $distanceFromTarget = abs($targetValue - $currentValue);
        $weight = 12 * ($distanceFromTarget / $scale);

        return abs(floor($weight));
    }
}
