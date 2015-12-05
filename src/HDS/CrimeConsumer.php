<?php
/**
 * This is the main Scraper for crime data.
 *
 * @package HRQLS
 * @subpackage HDS
 * @author Derek
 */

use elasticsearch\Client;
use Utils\Scraper;

/**
 * The consumer for Crime data.
 */
class CrimeConsumer
{

    /**
     * The scraper object handle.
     *
     * @var Scraper
     */
    public $scraper;

    /**
     * The city being scraped.
     *
     * @var string
     */
    public $city;

    /**
     * CrimeConsumer Constructor.
     *
     * @param string $city The city which the data is being consumed for.
     * @param string $url  The url to perform the scrap on.
     *
     * @return void
     */
    public function __construct($city, $url)
    {
        $this->scraper = new Scraper($url);
        $this->city = $city;
    }

    /**
     * Primary action function that consumes the data.
     *
     * @return void
     */
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
        } while (count($json)%35 === 0);
    }

    /**
     * The database interaction function.
     *
     * @param string $json The data to insert into elasticsearch.
     *
     * @return void
     */
    private function insertIntoElasticSearch($json)
    {
        $client = new Elasticsearch\Client(['hosts' => ['http://localhost:9200']]);
        $params = [];
        $params['index'] = 'hrqls';
        $params['type'] = 'crimedata';
        foreach ($json as $item) {
            $params['body'][] = array(
                'create' => array(
                    '_id' => sha1($item['link'])
                  )
            );
            $params['body'][] = $item;
        }
        print_r($params);
        $client->bulk($params);

    }
}
