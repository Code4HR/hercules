<?php
/**
 * Defines a Client to interface with the Zillow housing API.
 *
 * @package HDS
 */
namespace HDS;

/**
 * Class that retrieves data from Zillow housing service.
 */
class ZillowClient
{
    const  API_KEY = 'X1-ZWz1ezml90agaz_4234r';
    const BASE_URL = 'http://zillow.com/webservice/GetDemographics.htm?zws-id=';

    /**
     * Class constructor.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Queries the Zillow API for a specific zipcode.
     *
     * @param string $zip The zipcode we are fetching data for.
     *
     * @return array
     */
    public function getDataByZipCode($zip)
    {
        if (!in_array($zip, static::$zips)) {
            return [];
        }

        $url = self::BASE_URL . self::API_KEY . '&zip=' . $zip;

        $responseData = json_encode(simplexml_load_string(file_get_contents($url)));

        $response = json_decode($responseData, true);
        if (!isset($response['response'])) {
            return [];
        }
        $response = $response['response'];

        $json = [
            'state' => '',
            'city' => '',
            'zip' => $zip,
            'location' =>[
                'lon' => null,
                'lat' => null,
            ],
            'homeData' => [
                'avgHomeValueIndex' => null,
                'avgHomesRecentlySold' => null,
                'avgPropertyTax' => null,
                'turnoverWithinLastYear' => null,
            ],
        ];

        $json['state'] = $response['region']['state'];
        $json['city'] = $response['region']['city'];
        $json['zip'] = $zip;
        $json['location']['lon'] = $response['region']['longitude'];
        $json['location']['lat'] = $response['region']['latitude'];

        if (!isset($response['pages'])) {
            return $json;
        }
        $pages = $response['pages'];

        if (!isset($pages['page'][0]['tables']['table']['data']['attribute'])) {
            return $json;
        }
        $attributes = $pages['page'][0]['tables']['table']['data']['attribute'];

        foreach ($attributes as $item) {
            switch (strtoupper($item['name'])) {
                case "ZILLOW HOME VALUE INDEX":
                    if (isset($item['values']['zip'])) {
                        $json['homeData']['avgHomeValueIndex'] = $item['values']['zip']['value'];
                    }
                    break;
                case "HOMES RECENTLY SOLD":
                    if (isset($item['values']['zip'])) {
                        $json['homeData']['avgHomesRecentlySold'] = $item['values']['zip']['value'];
                    }
                    break;
                case "PROPERTY TAX":
                    if (isset($item['values']['zip'])) {
                        $json['homeData']['avgPropertyTax'] = $item['values']['zip']['value'];
                    }
                    break;
                case "TURNOVER (SOLD WITHIN LAST YR.)":
                    if (isset($item['values']['zip'])) {
                        $json['homeData']['turnoverWithinLastYear'] = $item['values']['zip']['value'];
                    }
                    break;
                default:
                    break;
            }
        }

        return $json;
    }

    /**
     * Retrieves Data from all zips in Hampton Roads.
     *
     * @return string
     */
    public function getDataForAllZips()
    {
        $json = [];
        foreach ($this->zips as $zip) {
            $json[] = $this->getDataByZipCode($zip);
        }

        return json_encode($json);
    }
}
