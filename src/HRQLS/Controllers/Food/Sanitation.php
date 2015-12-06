<?php
/**
 * Controller for Food Sanitization API endpoint.
 *
 * @package HRQLS/Controllers
 */

namespace HRQLS\Controllers\Food;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sanitization endpoint for the API.
 */
class Sanitation
{
    /**
     * City endpoint list for facade data pull.
     *
     * @var array
     */
    public $cities = [
        'norfolk' => 'http://api.openhealthinspection.com/vendors?after=24-10-2014&city=norfolk',
        'portsmouth' => 'http://api.openhealthinspection.com/vendors?after=24-10-2014&city=portsmouth',
        'virginiabeach' => 'http://api.openhealthinspection.com/vendors?after=24-10-2014&city=virginia%20beach',
        'suffolk' => 'http://api.openhealthinspection.com/vendors?after=24-10-2014&city=suffolk',
        'newportnews' => 'http://api.openhealthinspection.com/vendors?after=24-10-2014&city=newport%20news',
        'hampton' => 'http://api.openhealthinspection.com/vendors?after=24-10-2014&city=hampton',
        'chesapeake' => 'http://api.openhealthinspection.com/vendors?after=24-10-2014&city=chesapeake'
    ];

    /**
     * Main entrypoint for Santization endpoint.
     *
     * @param Request     $req The request object.
     * @param Application $app The Silex application object.
     *
     * @return Response
     */
    public function main(Request $req, Application $app)
    {
        $sliderPercentage = $req->get('slidervalue');
        $response = [];
        foreach ($this->cities as $city => $url) {
            $result = file_get_contents($url);
            array_push($response, json_decode($result, true));
        }

        $sanitationdata = [];
        foreach ([50, 75, 80, 90] as $value) {
            $updatedslidervalue = (($sliderPercentage * (100 - $value) ) / 100) + $value;
            //echo $updatedslidervalue;exit;
            $sanitationdata = [];
            foreach ($response as $key => $res) {
                $apidata = [];
                foreach ($res as $toldata) {
                    $data['name'] = $toldata['name'];
                    $data['latitude'] = $toldata['coordinates']['latitude'];
                    $data['longitude'] = $toldata['coordinates']['longitude'];
                    $data['city'] = $toldata['city'];
                    $score = $toldata['score'];
                    $apidata[] = $data['city'];
                    if ($score >= $updatedslidervalue) {
                        $sanitationdata[] = $data;
                    }
                    
                    if (count($apidata) > 50) {
                        break;
                    }
                }
            }
            if (count($sanitationdata)) {
                break;
            }
        }

        return new Response(json_encode($sanitationdata), 201);
    }
}
