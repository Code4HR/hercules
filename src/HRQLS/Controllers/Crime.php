<?php

namespace HRQLS\Controllers;

use Silex\Application as Application;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Elasticsearch\Client;

class Crime
{
    public function main(Request $req, Application $app)
    {
       $client = new Client(['hosts' => ['http://localhost:9200']]);
       $sliderPercentage = $req->get('slidervalue') / 100; 
       $slider = floor($sliderPercentage * 12) ;
       $params = array(
            'index'  => 'hrqls',
            'type'   => 'crimedata',
            'body' => [
                'query' => [
                    'range' => [
                        'severity' => [
                            'gte' => $slider
                        ]
                    ]
                ]
            ] 
       );
       $result = $client->search($params)['hits'];
       return new Response(json_encode($result), 201);
    } 
}
