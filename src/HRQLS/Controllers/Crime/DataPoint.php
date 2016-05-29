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
     * @var array List of valid crime categories.
     */
    const CATEGORIES = ['FELONY', 'MISDEMEANOR', 'CITATION', 'REPORT'];
    
    /**
     *
     */
    const CLASSES = [
        'FELONY' => [ '1', '2', '3', '4', '5', '6' ],
        'MISDEMEANOR' => ['1', '2', '3', '4'],
        'CITATION' => ['0'],
        'REPORT' => ['0'],
    ];
    
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
     * @throws \InvalidArgumentException When $category is not 'FELONY', 'MISDEMEANOR'.
     * @throws \InvalidArgumentException When $class is not valid for the specified category.
     */
    public function __construct($offense, $category, $class, \DateTime $date, $city, array $location)
    {
        $severity = self::assignClassAndCategory($offense);
        
        if (empty($category)) {
            $category = $severity['Category'];
        }
        
        //Ensure the category is one of the specified categories in the class constant CATEGORIES.
        if (!in_array(strtoupper($category), self::CATEGORIES)) {
            $validCategories = implode(' ', self::CATEGORIES);
            throw new \InvalidArgumentException(
                "Category must be {$validCategories}. {$category} is not a valid crime category."
            );
        }
        
        if (empty($class)) {
            $class = $severity['Class'];
        }
        
        //Ensure the class specified is a valid class for the category of crime listed.
        if (!in_array($class, self::CLASSES[strtoupper($category)])) {
            $validClasses = implode(' ', self::CLASSES[strtoupper($category)]);
            throw new \InvalidArgumentException(
                "You specified an invalid class for a {$category}. Valid classes for a {$category} are {$validClasses}."
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
     * Converts the crime datapoint to an associative array object
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
    public function toArray()
    {
        return [
           'offense' => $this->offense,
           'category' => $this->category,
           'class' => $this->class,
           'occurred' => $this->occured,
           'city' => $this->city,
           'locaton' => $this->location,
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
    
    /**
     * Assigns a class and category to the specified offense.
     * Felonies can be assigned a class of 1,2 ,3, 4, 5, or 6 where 1 is most severe and 6 is least severe.
     * Misdemeanors can be assigned a class of 1, 2, 3, or 4 where 1 is the most severe and 4 is least severe.
     * All other offenses/reports are assigned a class of 0.
     *
     * The Following resources were used to determine the crime mappings.
     *     http://law.lis.virginia.gov/vacode
     *     http://www.criminaldefenselawyer.com/
     *
     * @param string $offense The offense to map a category and class for.
     *
     * @return array like [
     *    'Category' => FELONY | MISDEMEANOR | CITATION | REPORT,
     *    'Class' => 1-6 for Felonies, 1-4 for misdemeanors otherwise 0,
     * ];
     */
    private function assignClassAndCategory($offense)
    {
        switch (strtoupper($offense))
        {
            //Felonies in descending order of severity.
            case 'AGGRAVATED ASSAULT':
            case 'HOMICIDE':
            case 'MURDER':
            case 'SHOOTING LOW PR':
                return ['Category' => 'FELONY', 'Class' => 1];
            case 'ROBBERY, BUSINESS':
            case 'ROBBERY INDIVID':
            case 'ROBBERY INDIV L':
            case 'ROBBERY, PERSON':
                return ['Category' => 'FELONY', 'Class' => 2];
            case 'LARCENY':
            case 'LARCENY,OF M.V. PARTS OR ACCESSORIES':
            case 'LARCENY, FROM MOTOR VEHICLE':
            case 'LARCENY, FROM BUILDING':
            case 'MAIMING':
                return ['Category' => 'FELONY', 'Class' => 3];
            case 'EMBEZZLEMENT':
            case 'PROSTITUTION':
                return ['Category' => 'FELONY', 'Class' => 4];
            case 'BURGLARY/ B & E, COMMERCIAL':
            case 'BURGLARY/ B & E, RESIDENTIAL':
            case 'WEAPON LAW VIOLATIONS - ALL OTHERS':
            case 'BATTERY':
                return ['Category' => 'FELONY', 'Class' => 5];
            case 'COUNTERFEITING/ FORGERY, ALL OTHERS':
            case 'LARCENY LOW PRI':
            case 'LARCENY NORMAL':
            case 'LARCENY, SHOPLIFTING':
            case 'LARCENY, POCKET PICKING':
            case 'LARCENY, ALL OTHERS':
            case 'FRAUD, CREDIT CARD':
            case 'FRAUD, BY PRESCRIPTION':
            case 'STOLEN VEH LOW':
            case 'MOTOR VEHICLE THEFT - AUTOMOBILE':
            case 'TRANSMITTING AN STD':
                return ['Category' => 'FELONY', 'Class' => 6];
            //Misdemeanors in descending order of severity
            case 'ASSAULT':
            case 'ASSAULT, SIMPLE':
            case 'ASSAULT LOW PRI':
            case 'DESTRUCTION OF PROPERTY, PRIVATE PROPERTY':
            case 'DESTRUCTION OF PROPERTY, CITY PROPERTY':
            case 'DESTRUCTION OF PROPERTY, CITY - GRAFFITI':
            case 'DOMESTIC VIOLENCE':
            case 'VANDALISM':
            case 'FRAUD, INNKEEPER':
            case 'FRAUD, BY PRESCRIPTION':
            case 'FRAUD LOW PRIOR':
            case 'FRAUD, USE FALSE NAME':
            case 'FRAUD, ALL OTHERS':
            case 'DRUG/ NARCOTIC VIOLATIONS':
            case 'UNDERAGE DRINKING':
            case 'MINOR IN POSSESSION OF ALCOHOL':
                return ['Category' => 'MISDEMEANOR', 'Class' => 1];
            case 'POSSESSION OF CONTROLLED SUBSTANCE':
            case 'POSSESSION OF DRUG PARAPHENALIA':
                return ['Category' => 'MISDEMEANOR', 'Class' => 2];
            case 'TRESPASS OF REAL PROPERTY':
                return ['Category' => 'MISDEMEANOR', 'Class' => 3];
            case 'PUBLIC INTOXICATION':
                return ['Category' => 'MISDEMEANOR', 'Class' => 4];
            //Non-classable crimes are set as a citation i.e. Parking or Speeding tickets etc.
            case 'ALL OTHER REPORTABLE OFFENSES':
                return ['Category' => 'CITATION', 'Class' => 0];
            //Some precincts report stolen property, lost animals and other non-crimal incidents.
            default:
                return ['Category' => 'REPORT', 'Class' => 0];
        }
    }
}
