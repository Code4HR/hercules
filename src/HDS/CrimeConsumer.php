<?php

include __DIR__ . '/../../vendor/autoload.php';
use elasticsearch\Client;
use Utils\Scraper;

$consumer = new CrimeConsumer('VA Beach', 'http://hamptonroads.com/newsdata/crime/virginia-beach/search/rss?type=&near=&radius=&from%5Bmonth%5D=10&from%5Bday%5D=1&from%5Byear%5D=2015&to%5Bmonth%5D=10&to%5Bday%5D=23&to%5Byear%5D=2015&op=Submit&form_id=crime_searchform');
$consumer->consume();
$consumer = new CrimeConsumer('Norfolk', 'http://hamptonroads.com/newsdata/crime/norfolk/search/rss?me=%2Fnorfolk%2Fsearch&type=&near=&radius=&op=Submit&form_token=9dc84572393ad9c68f54cad6549692f3&form_id=crime_searchform');
$consumer->consume();
$consumer = new CrimeConsumer('Portsmouth', 'http://hamptonroads.com/newsdata/crime/portsmouth/search/rss?type=&near=&radius=&from%5Bmonth%5D=10&from%5Bday%5D=1&from%5Byear%5D=2015&to%5Bmonth%5D=10&to%5Bday%5D=23&to%5Byear%5D=2015&op=Submit&form_id=crime_searchform');
$consumer->consume();
$consumer = new CrimeConsumer('Suffolk', 'http://hamptonroads.com/newsdata/crime/suffolk/search/rss?type=&near=&radius=&from%5Bmonth%5D=10&from%5Bday%5D=1&from%5Byear%5D=2015&to%5Bmonth%5D=10&to%5Bday%5D=23&to%5Byear%5D=2015&op=Submit&form_id=crime_searchform');
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
           $json += $this->scraper->scrapeCrime($currPage, $this->city);
           if (sizeof($json) > 0) {
               $this->insertIntoElasticSearch($json);
           }
           $prevPage = $currPage;
           $currPage++;
        }
        while (count($json)%35 === 0);

        foreach($json as $crime) {
            echo $crime . PHP_EOL;
        }
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
