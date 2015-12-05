<?php
/**
 * Controller for Cost of Living endpoint.
 *
 * @package HRQLS/Controllers
 */
namespace HRQLS\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Elasticsearch\Client;

/**
 * Controller for Cost of Living endpoint.
 */
class CoL
{
    /**
     * Method that handles the main entrypoint for the cost of living endpoint.
     *
     * @param Request     $req The request object.
     * @param Application $app The silex application object.
     *
     * @return Response
     */
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
            $sliderInfo = $this->calculate($slidervalue);
            $weight = $this->determineWeight(
                $sliderInfo,
                $zip['_source']['avgHomeValueIndex'],
                $averageHouse,
                $maxHouseValue,
                $minHouseValue
            );
            $responseObject[] = array(
                'lat' => $zip['_source']['location']['lat'],
                'lon' => $zip['_source']['location']['lon'],
                'weight' => $weight
            );
        }

        return new Response(json_encode($responseObject), 200);
    }

    /**
     * Method for calculating the direction and distance from the mid point.
     *
     * @param string $slider Value the slider has.
     *
     * @return array
     */
    private function calculate($slider)
    {
        $direction = false;
        $distance = 0;
        if ($slider >= 50) {
            $direction = true;
            $distance = ((($slider - 50) * -1) + 50) / 50;
        } else {
            $distance = $slider / 50;
        }

        return array('direction' => $direction, 'distance' => $distance);
    }

    /**
     * Determines the weight of a given value based on a relative scale.
     *
     * This function probably needs some help.  It didn't seem to work right in the hackathon.
     *
     * @param array  $sliderInfo   The information on the slider.
     * @param string $currentValue The currentValue of the zip.
     * @param string $average      The average value.
     * @param string $max          The maximum value.
     * @param string $min          The minimum value.
     *
     * @return string
     */
    private function determineWeight(array $sliderInfo, $currentValue, $average, $max, $min)
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
