<?php

namespace HRQLS\Controllers;
namespace HRQLS\Controllers\Food;
use Silex\Application as Application;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;

class Sanitation
{
    public $cities = [
        'norfolk' => 'http://api.openhealthinspection.com/vendors?city=norfolk',
        'portsmouth' => 'http://api.openhealthinspection.com/vendors?city=portsmouth',
        'virginiabeach' => 'http://api.openhealthinspection.com/vendors?city=virginia%20beach'
    ];

    public function main(Request $req, Application $app)
    {
        $sliderPercentage = $req->get('slidervalue');
        foreach($this->cities as $city => $url) {
            $result = file_get_contents($url);
            $response = json_decode($result, true);
        }
        
        $sanitationdata = [];
        foreach($response as $key => $res) {
            $data['latitude'] = $res['coordinates']['latitude'];
            $data['longitude'] = $res['coordinates']['longitude'];
            $data['score'] = $res['score'];
            $sanitationdata[] = $data;
        }

        return new Response(json_encode($sanitationdata), 201);
    } 
}
