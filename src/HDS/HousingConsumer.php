<?php
/**
 * Defines a Consumer for Housing Data
 * Uses Zillow API Client to query for each City in the Hampton Roads area.
 * Gets all Neighborhoods for each City
 * Gets following Data for each Neighborhod.
 * Inserts into ES
 */

 public final class HousingConsumer{

     private $zillow;
     private $elastic;
     $cities = array();

     public function __construct($zillowClient, $elasticCient)
     {
         $this->zillow = $zillowClient;
         $this->elastic = $elasticClient;
     }

     public function consume(\ZillowAPIClient $client)
     {

     }

     public function getNeighborhoods($city)
     {

     }

     public function getNeighborhoodData($neighborhood)
     {

     }
 }
