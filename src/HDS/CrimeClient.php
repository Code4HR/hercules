<?php
/**
 * This is the main Scraper for crime data.
 *
 * @package    HRQLS
 * @subpackage HDS
 * @author     Derek
 */
namespace HDS;

use elasticsearch\Client;
use Utils\Scraper;

/**
 * The consumer for Crime data.
 */
class CrimeClient
{

    /**
     * The number of items per page
     */
    const PER_PAGE = 35;

    /**
     * The Guzzle Client used to get site contents
     *
     * @var $client
     */
    public $client;

    /**
     * CrimeConsumer Constructor.
     *
     * @param Client $webClient Client used to retrieve data from Pilot Online Site.
     *
     * @return void
     */
    public function __construct(Client $webClient)
    {
        $this->client = $webClient;
    }

    /**
     * Primary action function that consumes the data.
     *
     * @param string $city    The city to pull crime date for.
     * @param string $cityUrl The City specific URL to the Virginia Pilots Crime Stats RSS Feed.
     *
     * @throws Exception A general exception.
     * @return void
     */
    public function consume($city, $cityUrl)
    {
        $respone = $this->Client->request('GET', $cityUrl);
        if (!$response->getStatusCode() === 200) {
            throw new Exception("Failed to pull crime date for {$city}");
        }

        //Everything below here probably needs to be reworked.
        $json = array();

        $currPage = 0;
        do {
            $json = $this->scrapeCrime($currPage, $this->city);
            if (sizeof($json) > 0) {
                $this->insertIntoElasticSearch($json);
            }
            $currPage++;
        } while (count($json)%35 === 0);
    }

    /**
     * Scrapes Crime Data.
     *
     * @param string  $city The city to scrape crime data foreach.
     * @param integer $page The current page number.
     *
     * @return boolean
     */
    public function scrapeCrime($city, $page = 0)
    {
        $url = $this->source . '&page=' . $page;

        $response = json_encode(simplexml_load_string(file_get_contents($url)));
        $data = json_decode($response, true);

        $jsonArray = [];
        $json = [
            'title' => null,
            'location' => [
                'lat' => null,
            'lon' => null,
            ],
            'link' => null,
            'date_occured' => new \DateTime(),
            'severity' => 0,
            'city' => null,
        ];

        $items = $data['channel'];

        foreach ($items['item'] as $item) {
            $title;
            $dateOccured;
            if (isset($item['title'])) {
                $start = $end = 0;
                $start = strpos($item['title'], '(');
                $end = strpos($item['title'], ')');
                $title = trim(substr(' ' . $item['title'], 0, $start));
                $dateOccured = \DateTime::CreateFromFormat('M d, Y', trim(substr($item['title'], $start+1, $end-1)));
                $json['title'] = $title;
                $json['date_occured'] = $dateOccured;
                $json['severity'] = $this->calcSeverity($title);
            }

            if (isset($item['loc'])) {
                $json['location']['lon'] = $item['loc']['lon'];
                $json['location']['lat'] = $item['loc']['lat'];
            }

            if (isset($item['link'])) {
                $json['link'] = $item['link'];
            }

            $json['city'] = $city;
            array_push($jsonArray, $json);
        }

        return $jsonArray;
    }

    /**
     * Calculates severity.
     *
     * @param string $crime The crime Name.
     *
     * @return int The severity of the crime.
     */
    private function calcSeverity($crime)
    {
        switch ($crime) {
            case 'Rape':
            case 'Sexual battery':
            case 'Bomb threat':
            case 'Statutory rape/ carnal knowledge':
                return 10;
            case 'Death investigation':
            case 'Attempted robbery':
            case 'Robbery':
            case 'Aggravated assault':
                return 9;
            case 'Assault, simple, domestic':
            case 'Simple assault':
            case 'Arson':
            case 'Attempted arson':
                return 8;
            case 'Weapons offense':
            case 'Pornography/ obscene material':
            case 'Vehicle theft':
            case 'Child abuse':
                return 7;
            case 'Destruction of property':
            case 'Attempted destruction of property':
            case 'Molesting':
            case 'Tampering with auto':
            case 'Forcible indecent liberties':
            case 'Indecent exposure':
                return 6;
            case 'Hit and run':
            case 'Threaten bodily harm':
            case 'Violation of protection order':
            case 'Extortion':
            case 'Attempted family offense, nonviolent, child abuse':
            case 'Child neglect':
            case 'Abduction/kidnapping':
            case 'Impersonating a police officer':
            case 'Attempted vehicle theft':
                return 5;
                //Misdemeanors
            case 'Dui':
            case 'Drug offense':
            case 'Fraud':
            case 'Attempted fraud':
            case 'Larceny':
            case 'Attempted suicide':
            case 'Attempted burglary':
            case 'Burglary':
            case 'Attempted larceny':
            case 'Attempted extortion':
            case 'Forgery':
            case 'Suicide attempt':
            case 'Overdose':
            case 'Suicide':
            case 'Child endangerment':
            case 'Stalking':
            case 'Attempted shoplifting':
            case 'Concealment/ price changing':
            case 'Attempted concealment/price changing':
            case 'Obstructing justice':
            case 'Liquor law violations':
            case 'Attempted counterfeiting/ forgery, all others':
                return 4;
            case 'Throwing object at moving vehicle':
            case 'Counterfeiting/forgery':
            case 'Unauthorized use of vehicle':
            case 'Cruelty to animals':
            case 'Embezzlement':
            case 'Contributing to the delinquency of a minor':
            case 'Peeping':
                return 3;
            case 'Missing person':
            case 'Immoral conduct':
            case 'Providing false information to police':
            case 'Trespassing':
            case 'Disturbing the peace':
            case 'Attempted trespass':
            case 'Disorderly conduct':
                return 2;
            case 'Attempted all other reportable offenses':
            case 'All other reportable offenses':
            case 'Annoying phone calls':
            case 'Runaway':
            case 'Cursing/ obscene language':
            case 'Obscene phone calls':
                return 1;
            default:
                return 0;
        }
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
