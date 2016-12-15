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
final class Sanitation
{
    /**
     * City endpoint list for facade data pull.
     *
     * @var array
     */
    public $cities = [
        'norfolk' => 'https://ohi-api.code4hr.org/vendors?after=24-10-2014&city=norfolk',
        'portsmouth' => 'https://ohi-api.code4hr.org/vendors?after=24-10-2014&city=portsmouth',
        'virginiabeach' => 'https://ohi-api.code4hr.org/vendors?after=24-10-2014&city=virginia%20beach',
        'suffolk' => 'https://ohi-api.code4hr.org/vendors?after=24-10-2014&city=suffolk',
        'hampton' => 'https://ohi-api.code4hr.org/vendors?after=24-10-2014&city=hampton',
        'chesapeake' => 'https://ohi-api.code4hr.org/vendors?after=24-10-2014&city=chesapeake'
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
                $apidata[] = $data['city'];
                    $sanitationdata[] = $data;
            }
        }

        return new Response($_GET['callback'] . '('.json_encode($sanitationdata).')', 201);
    }
}
