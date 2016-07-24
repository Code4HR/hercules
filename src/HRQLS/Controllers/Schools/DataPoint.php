<?php
/**
 * Data Storage Object for Schools Data Points.
 *
 * @package HRQLS/Controllers
 */

namespace HRQLS\Controllers\Schools;

use HRQLS\Bootstrap;
use Silex\Application;

/**
 * Defines Schools DataPoint Class.
 * The purpose of this class is to standardize School Data from any city.
 */
final class DataPoint
{
    /**
     * The name of the school.
     *
     * @var string
     */
    private $name;
    
    /**
     * The type of school. Either Private or Public.
     *
     * @var string
     */
    private $type;
    
    /**
     * The range of grades available at this schoool.
     *
     * @var string
     */
    private $gradeRange;
    
    /**
     * The rating for this school as given by parents of children who attend the school.
     *
     * @var integer
     */
    private $rating;
    
    /**
     * Location of this school as described by latitude and longitude.
     *
     * @var array like [ 'lat' => (float), 'lon' => (float)].
     */
    private $location;
}
