<?php
/**
 * The Hercules standard response class.
 *
 * @package HRQLS\Models
 */

namespace HRQLS\Models;

use HRQLS\Exceptions\InvalidResponseException;

/**
 * This class standardizes the responses from hercules by providing a common structure for all responses.
 */
class HerculesResponse
{
    /**
     * This holds the status code for the response.
     *
     * This defaults to 200 as a convenience.
     *
     * @var integer
     */
    private $statusCode;

    /**
     * Holds the string for the endpoint portion of the response.
     *
     * @var string
     */
    private $endpoint;

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
     * Creates a new HerculesResponse Object from the specified endpoint, data array and errors array.
     *
     * @param string  $endpoint   The endpoint creating the response.
     * @param integer $statusCode The HTTP Status Code to be returned with this response. Defaults to 200.
     * @param array   $data       An array of data to be returned with the response. Defaults to an empty array.
     * @param array   $errors     An array of the errors that occured while building the response. empty by default.
     */
    public function __construct($endpoint, $statusCode = 200, array $data = [], array $errors = [])
    {
        $this->endpoint = $endpoint;
        $this->statusCode = $statusCode;
        $this->data = $data;
        $this->errors = $errors;
    }

    /**
     * Validation function that checks that the necessary parts of the response have been set.
     *
     * @return mixed true on success, otherwise an array of error messages.
     */
    private function verifyResponse()
    {
        $errorMessages = [];
        
        if (!isset($this->endpoint)) {
            $errorMessages[] = "$endpoint must be set.";
        }
        
        if (!isset($this->statusCode)) {
            $errorMessages[] = "$statusCode must be set.";
        }
        
        if (!is_array($this->data)) {
            $errorMessages[] = "$data must be formatted as an array.";
        }
        
        if (!is_array($this->errors)) {
            $errorMessages[] = "$errors must be formatted as an array.";
        }

        if (!empty($errorMessages)) {
            return $errorMessages;
        }
        
        return true;
    }

    /**
     * Gets the current status code.
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    
    /**
     * Gets the endpoint value for this response
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
    
    /**
     * Gets the data beign returned with this response.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Gets the errors array returned with this response.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Appends the specified data to the end of the current data array.
     *
     * @param array $data The data to add to the response.
     *
     * @return void
     */
    public function addDataEntry(array $data)
    {
        //According to this SO post
        //http://stackoverflow.com/questions/559844/whats-better-to-use-in-php-array-value-or-array-pusharray-value
        //array[] is more efficient that array_push
        $this->data[] = $data;
    }

    /**
     * Appends the specified errors to the end of the current errors array.
     *
     * @param array $error The error to add.
     *
     * @return void
     */
    public function addError(array $error)
    {
        $this->errors[] = $error;
    }

    /**
     * Retrieves the hercules formatted response.
     *
     * @return string The JSON response.
     *
     * @throws InvalidResponseException If the current state of the response object fails validation.
     */
    public function to_json()
    {
        $status = $this->verifyResponse();
        if (true != $status) {
            throw new InvalidResponseException('Bad response setup. ' . implode(" \n", $status));
        }

        $response = [
          endpoint => $this->endpoint,
          datetime => time(),
          statusCode => $this->statusCode,
          'data' => $this->data,
          'errors' => $this->errors,
        ];

        return json_encode($response);
    }
}
