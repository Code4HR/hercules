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
use Hercules\Response as HerculesResponse;

/**
 * Sanitization endpoint for the API.
 */
final class Sanitation
{
    /**
     * City endpoint list for facade data pull.
     *
     * @var array
     */
    public $cities = [
        'norfolk' => 'https://ohi-api.code4hr.org/vendors?city=norfolk',
        'portsmouth' => 'https://ohi-api.code4hr.org/vendors?city=portsmouth',
        'virginiabeach' => 'https://ohi-api.code4hr.org/vendors?city=virginia%20beach',
        'suffolk' => 'https://ohi-api.code4hr.org/vendors?city=suffolk',
        'hampton' => 'https://ohi-api.code4hr.org/vendors?city=hampton',
        'chesapeake' => 'https://ohi-api.code4hr.org/vendors?city=chesapeake'
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
        $response = [];
        foreach ($this->cities as $city => $url) {
            $result = file_get_contents($url);
            array_push($response, json_decode($result, true));
        }

        foreach ($response as $key => $res) {
            $apidata = [];
            foreach ($res as $toldata) {
                $data['name'] = $toldata['name'];
                $data['latitude'] = $toldata['coordinates']['latitude'];
                $data['longitude'] = $toldata['coordinates']['longitude'];
                $data['city'] = $toldata['city'];
                $data['score'] = $toldata['score'];
                $data['address'] = $toldata['address'];
                $data['type'] = $toldata['type'];
                $data['category'] = $toldata['category'];
                $data['url'] = $toldata['url'];
                $apidata[] = $data['city'];
                    $sanitationdata[] = $data;
            }
        }

        $herculeanResponse = new HerculesResponse('/Food/Sanitation', new \DateTime(), 200, $sanitationdata, []);

        return $_GET['callback'] . '(' . $herculeanResponse->to_json() . ')';
    }
}
