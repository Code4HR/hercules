<?php

namespace HRQLS\Controllers;
namespace HRQLS\Controllers\Food;
use Silex\Application as Application;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;

class Sanitation
{
    public $cities = [
        'norfolk' => 'http://api.openhealthinspection.com/vendors?after=24-10-2014&city=norfolk',
        'portsmouth' => 'http://api.openhealthinspection.com/vendors?after=24-10-2014&city=portsmouth',
        'virginiabeach' => 'http://api.openhealthinspection.com/vendors?after=24-10-2014&city=virginia%20beach',
        'suffolk' => 'http://api.openhealthinspection.com/vendors?after=24-10-2014&city=suffolk',
        'newportnews' => 'http://api.openhealthinspection.com/vendors?after=24-10-2014&city=newport%20news'
    ];

    public function main(Request $req, Application $app)
    {
        $sliderPercentage = $req->get('slidervalue');
        $response = [];
        foreach($this->cities as $city => $url) {
            $result = file_get_contents($url);
            array_push($response, json_decode($result, true));
        }

        $sanitationdata = [];
        foreach ([50, 75, 80, 90] as $value) {
            $updatedslidervalue = (($sliderPercentage * (100 - $value) ) / 100) + $value;
            $sanitationdata = [];
            foreach($response as $key => $res) { 
                foreach ($res as $toldata) {
                    $data['name'] = $toldata['name'];
                    $data['latitude'] = $toldata['coordinates']['latitude'];
                    $data['longitude'] = $toldata['coordinates']['longitude'];
                    $data['city'] = $toldata['city'];
                    $score = $toldata['score'];
                    if ($score >= $updatedslidervalue) {
                        $sanitationdata[] = $data;
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
