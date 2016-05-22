<?php
/**
 * Data Storage Object for Crime Data Points.
 *
 * @package HRQLS/Controllers
 */

namespace HRQLS\Controllers\Crime;

/**
 * Defines the DataPoint class.
 * The purpose of this class is to standardize crime data from any city.
 */
final class DataPoint
{
    /**
     *
     */
    const CATEGORIES = ['FELONY', 'MISDEMEANOR'];
    
    /**
     * @var string The offense that was reported.
     */
    private $offense;
    
    /**
     * @var string The category of the crime either Felony or Misdemeanor
     */
    private $category;
    
    /**
     * @var string The classification of the Crime 1, 2, 3 etc. This differs between the 2 categories.
     */
    private $class;
    
    /**
     * @var DateTime The timestamp of when the crime was reported.
     */
    private $occured;
    
    /**
     * @var string The city in which the crime occured.
     */
    private $city;
    
    /**
     * @var array The lat/log coordinates of where the crime occured.
     */
    private $location;
    
    /**
     * Creates a new Crime DataPoint from the given parameters.
     *
     * @param string    $offense  The crime that was reported i.e. Burglary.
     * @param string    $category The category of the crime i.e. Felony | Misdemeanor.
     * @param string    $class    The class of the crime i.e. 1-5 etc.
     * @param \DateTime $date     The timestamp of when the crime was reported.
     * @param string    $city     The city in which the crime occured.
     * @param array     $location The lat/lon coordinates where the crime took place.
     *
     * @return void
     *
     * @throws \InvalidArgumentException When $category is not 'FELONY' or 'MISDEMEANOR'.
     */
    public function __construct($offense, $category, $class, \DateTime $date, $city, array $location)
    {
        if (!in_array(strtoupper($category), self::CATEGORIES)) {
            $validCategories = implode(' ', self::CATEGORIES);
            throw new \InvalidArgumentException(
                "Category must be {$validCategories}. {$category} is not a valid crime category."
            );
        }
        
        $this->offense = $offense;
        $this->category = strtoupper($category);
        $this->class = $class;
        $this->occured = $date->format('Y-m-d H:i:s');
        $this->city = $city;
        //TODO Probably need to do some kind of filtering for location.
        $this->location = $location;
    }
    
    /**
     * Converts the crime datapoint to a JSON object
     *
     * @return array Like [
     *   'offense' => '',
     *   'category' => '',
     *   'class' => '',
     *   'occured' => 'Y-m-d H:i:s' @see php DateTime::format,
     *   'city' => 'Hampton',
     *   'location' => [
     *     'lat' => (Float),
     *     'lon' => (Float),
     *   ];
     */
    public function toJson()
    {
        return json_encode([
           'offense' => $this->offense,
           'category' => $this->category,
           'class' => $this->class,
           'occurred' => $this->occured,
           'city' => $this->city,
           'locaton' => $this->location,
        ]);
    }

    /**
     * Returns the crime datapoint as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'offense' => $this->offense,
            'category' => $this->category,
            'class' => $this->class,
            'occurred' => $this->occured,
            'city' => $this->city,
            'location' => $this->location
        ];
    }
    
    /**
     * Gets this crimes offense.
     *
     * @return string
     */
    public function getOffense()
    {
        return $this->offense;
    }
    
    /**
     * Gets this crimes category.
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * Gets this crimes class.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
    
    /**
     * Gets the date this crime occured.
     *
     * @return string like 'Y-m-d H:i:s'
     */
    public function getOccured()
    {
        return $this->occured;
    }
    
    /**
     * Gets this city this crime was commited in.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Gets the lat/lon coordinates of where the crime occured.
     *
     * @return array like [
     *   'lat' => (Float),
     *   'lon' => (Float),
     * ];
     */
    public function getLocation()
    {
        return $this->location;
    }
}
