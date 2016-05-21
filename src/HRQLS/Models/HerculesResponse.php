<?php
/**
 * The Hercules standard response class.
 *
 * @package HRQLS\Models
 */

namespace HRQLS\Models;

use Symfony\Component\HttpFoundation\Response;

/**
 * This class standardizes the responses from hercules by providing a common structure for all responses.
 */
class HerculesResponse
{
    /**
     * This holds the status code for the response.
     *
     * This defaults to 200 as a convienance.
     *
     * @var Integer
     */
    private $statusCode = 200;

    /**
     * Holds the string for the endpoint portion of the response.
     *
     * @var String
     */
    private $endpoint = null;

    /**
     * The main dataset to be returned.
     *
     * @var Array
     */
    private $data = [];

    /**
     * Keeps track of any errors in the current response.
     *
     * @var Array
     */
    private $errors = [];

    /**
     * The class constructor.
     */
    public function __construct()
    {
    }

    /**
     * Allows the setting of the endpoint name to return in the response.
     *
     * @param String $endpoint The endpoint name.
     */
    public function setEndpoint($endpoint)
    {
        // Might be nice to have a validation process here to make sure it's not a bogus endpoint.

        $this->endpoint = $endpoint;
    }

    /**
     * Validation function that checks that the necessary parts of the response have been set.
     *
     * @return Boolean
     */
    private function verifyResponse()
    {
        if ($this->endpoint === null) {
            return false;
        }

        return true;
    }

    /**
     * Allows access to the current status code.
     *
     * @return String
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Allows the adding of single data entry to the data array of the response object.
     *
     * @param Array $data The data to add to the response.
     *
     * @return void
     */
    public function addDataEntry($data)
    {
        array_push($this->data, $data);
    }

    /**
     * Allows the registering of errors to the response object.
     *
     * @param Array $error The error to add.
     *
     * @return void
     */
    public function addError($error)
    {
        array_push($this->errors, $error);
    }

    /**
     *  Retrieves the hercules formatted response.
     *
     *  @return String The JSON response.
     */
    public function to_json()
    {
        if (!$this->verifyResponse()) {
            throw new \Exception("Endpoint not functioning correctly.  Bad response setup.");
        }

        $responseArray = [
          'endpoint' => $this->endpoint,
          'datetime' => time()
        ];

        if (sizeof($this->errors) > 0) {
            $responseArray['errors'] = $this->errors; 
            $responseArray['data'] = [];
        } else {
            $responseArray['data'] = $this->data;
            $responseArray['errors'] = [];
        }

        return json_encode($responseArray);
    }
}
