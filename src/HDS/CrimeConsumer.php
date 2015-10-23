<?php

include __DIR__ . '/../../vendor/autoload.php';
use elasticsearch\Client;
use Utils\Scraper;
/**
*[
*    [
*        {
*            "title": "Attempted robbery (October 21, 2015)",
*            "description": "2700 block of North Mall Drive",
*            "link": "http://hamptonroads.com/newsdata/crime/virginia-beach/detail/1984111",
*            "pubdate": "Thu, 22 Oct 2015 09:00:17 -0400",
*            "longitude": "-76.0670637",
*            "latitude": "36.8206927"
*        }
*    ]
*]
*/

$consumer = new CrimeConsumer('VA Beach', 'http://hamptonroads.com/newsdata/crime/virginia-beach/search/rss?me=/virginia-beach/search&type=&near=&radius=&op=Submit&form_token=9dc84572393ad9c68f54cad6549692f3&form_id=crime_searchform');
$consumer->consume();

class CrimeConsumer{

    public $scraper;
    public $city;

    public function __construct($city, $url)
    {
        $this->scraper = new Scraper($url);
        $this->city = $city;
    }

    public function consume()
    {
        header('Content-Type: application/json');
        $json = array();

        $currPage = 0;
        $prevPage = 0;
        do {
           $json = $this->scraper->scrapeCrime($currPage, $this->city);
           if (sizeof($json) > 0) {
               $this->insertIntoElasticSearch($json);
           }
           $prevPage = $currPage;
           $currPage++;
        }
        while (count($json)%35 === 0);


        //$jsonToInsert = $this->scraper->scrape();
    }

    private function insertIntoElasticSearch($json)
    {
        $client = new Elasticsearch\Client(['hosts' => ['http://localhost:9200']]);
        $params = [];
        $params['index'] = 'hrqls';
        $params['type'] = 'crimedata';
        foreach($json as $item) {
            $params['body'][] = array(
                'create' => array(
                    '_id' => sha1($item['link'])
                  )
            );
            $params['body'][] = $item;
        }
        $client->bulk($params);

    }
}
