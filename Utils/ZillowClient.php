<?php
/**
 * Defines a Client to interface with the Zillow housing API.
 */
namespace Utils;

/**
  *
  */
 class ZillowClient
 {
     const  API_KEY = 'X1-ZWz1ezml90agaz_4234r';
     const BASE_URL = 'http://zillow.com/webservice/GetDemographics.htm?zws-id=';
     static $zips = [
       23320,
       23321,
       23322,
       23323,
       23324,
       23325,
       23432,
       23433,
       23434,
       23435,
       23436,
       23437,
       23438,
       23451,
       23452,
       23454,
       23455,
       23456,
       23457,
       23459,
       23461,
       23462,
       23464,
       23502,
       23503,
       23504,
       23505,
       23507,
       23508,
       23509,
       23510,
       23513,
       23517,
       23518,
       23521,
       23523,
       23601,
       23602,
       23603,
       23604,
       23605,
       23606,
       23607,
       23608,
       23651,
       23661,
       23662,
       23663,
       23664,
       23665,
       23666,
       23669,
       23690,
       23691,
       23692,
       23693,
       23696,
       23701,
       23702,
       23703,
       23704,
       23707,
       23708,
     ];

     public function __construct()
     {
     }

   /**
    * Queries the Zillow API for a specific zipcode
    * @param $zip the zipcode we are fetching data for
    *
    * @return json array
    */
   public function getDataByZipCode($zip)
   {
       if (!in_array($zip, static::$zips)) {
           return [];
       }

       $url = self::BASE_URL . self::API_KEY . '&zip=' . $zip;

       $responseData = json_encode(simplexml_load_string(file_get_contents($url)));

       $response = json_decode($responseData, true);
       if(!isset($response['response'])) {
          return  [];
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

       @$json['state'] = $response['region']['state'];
       @$json['city'] = $response['region']['city'];
       @$json['zip'] = $zip;
       @$json['location']['lon'] = $response['region']['longitude'];
       @$json['location']['lat'] = $response['region']['latitude'];

       if (!isset($response['pages'])) {
           return $json;
       }
       $pages = $response['pages'];

       if (!isset($pages['page'][0]['tables']['table']['data']['attribute'])) {
           return $json;
       }
       $attributes = $pages['page'][0]['tables']['table']['data']['attribute'];

       foreach($attributes as $item) {
           switch(strtoupper($item['name']))
           {
              case "ZILLOW HOME VALUE INDEX":
                if (isset( $item['values']['zip'])){
                    $json['homeData']['avgHomeValueIndex'] = $item['values']['zip']['value'];
                    break;
                }
            case "HOMES RECENTLY SOLD":
                if(isset($item['values']['zip'])){
                    $json['homeData']['avgHomesRecentlySold'] = $item['values']['zip']['value'];
                    break;
                }
            case "PROPERTY TAX":
                if(isset($item['values']['zip'])){
                    $json['homeData']['avgPropertyTax'] = $item['values']['zip']['value'];
                    break;
                }
            case "TURNOVER (SOLD WITHIN LAST YR.)":
                if(isset($item['values']['zip'])){
                    $json['homeData']['turnoverWithinLastYear'] = $item['values']['zip']['value'];
                    break;
                }
            default:
                break;
           }
       }

       return $json;
   }

   public function getDataForAllZips()
   {
       $json = [];
       foreach(static::$zips as $zip) {
          $json[] = self::getDataByZipCode($zip);
       }

       return json_encode($json);
   }
 }
