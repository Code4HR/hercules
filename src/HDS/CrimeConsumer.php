<?php

include __DIR__ . '/../../vendor/autoload.php';
use elasticsearch\Client;
use Utils\Scraper;

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
        //do {
           $json = $this->scraper->scrapeCrime($currPage, $this->city);
           echo sizeof($json);
           $this->insertIntoElasticSearch($json);
           $prevPage = $currPage;
           $currPage++;
        //}
        //while (count($json[$prevPage])%35 === 0);
    }

    private function insertIntoElasticSearch($json)
    {
        $client = new Elasticsearch\Client(['hosts' => ['http://localhost:9200']]);
        $params = ['index' => 'hrqls', 'type' => 'crimedata'];
        foreach($json as $item) {
            $params['body'][] = array(
            'doc_as_upsert' => 'true',
            'doc' => $item
            );
        }
        $client->bulk($params);

    }
}
