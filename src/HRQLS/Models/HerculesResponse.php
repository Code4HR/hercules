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
     * @var integer
     */
    private $statusCode = 200;

    /**
     * Holds the string for the endpoint portion of the response.
     *
     * @var string
     */
    private $endpoint = null;

    /**
     * The main dataset to be returned.
     *
     * @var array
     */
    private $data = [];

    /**
     * Keeps track of any errors in the current response.
     *
     * @var array
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
     * @param string $endpoint The endpoint name.
     *
     * @return void
     */
    public function setEndpoint($endpoint)
    {
        // Might be nice to have a validation process here to make sure it's not a bogus endpoint.

        $this->endpoint = $endpoint;
    }

    /**
     * Validation function that checks that the necessary parts of the response have been set.
     *
     * @return boolean
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
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Allows the adding of single data entry to the data array of the response object.
     *
     * @param array $data The data to add to the response.
     *
     * @return void
     */
    public function addDataEntry(array $data)
    {
        array_push($this->data, $data);
    }

    /**
     * Allows the registering of errors to the response object.
     *
     * @param array $error The error to add.
     *
     * @return void
     */
    public function addError(array $error)
    {
        array_push($this->errors, $error);
    }

    /**
     * Retrieves the hercules formatted response.
     *
     * @return string The JSON response.
     *
     * @throws \Exception If the current state of the response object fails validation.
     */
    public function to_json()
    {
        if (!$this->verifyResponse()) {
            throw new \Exception("Endpoint not functioning correctly.  Bad response setup.");
        }

        $responseArray = [
          endpoint => $this->endpoint,
          datetime => time()
        ];

        if (sizeof($this->errors) > 0) {
            $responseArray['errors'] = $this->errors;
            $responseArray['data'] = [];
        } else {
            $responseArray['data'] = $this->data;
            $responseArray['errors'] = [];
        }

        return $responseArray;
    }
}