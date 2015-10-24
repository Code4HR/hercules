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
                'from' => 0,
                'size' => 10000,
                'query' => [
                    'range' => [
                        'severity' => [
                            'gte' => $slider
                        ]
                    ]
                ]
            ] 
       );
       $result = $client->search($params);
       $node = [];
       if ($result['hits']['total'] > 0) {
            $node = $result['hits']['hits'];
       }
       
       $crimedata = [];
       foreach ($node as $field => $crimes) {
           $data['title'] = $crimes['_source']['title'];
           $data['latitude'] = $crimes['_source']['location']['lat'];
           $data['longitude'] = $crimes['_source']['location']['lon'];
           $data['severity'] = $crimes['_source']['severity'];
           $crimedata[] = $data;
       }

       return new Response(json_encode($crimedata), 201);
    } 
}
