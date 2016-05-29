<?php
/**
 * Defines a custom exception thrown by the HRQLS framework.
 *
 * @package HRQLS/Exceptions
 */
 
namespace HRQLS\Exceptions;
 
 /**
  * Defines the custom InvalidResponseException class.
  * This exception should be thrown when a an invalid Hercules Response Object is being used.
  * An invalid response object is when one of the following occures:
  *     - endpoint is not set.
  *     - statusCode is not set.
  *     - data is not an array.
  *     - errors is not an array.
  */
class InvalidResponseException extends \Exception
{
    /**
     * Constructs a new InvalidResponseException object.
     *
     * @param string    $message  The message describinng why the exception occured.
     * @param integer   $code     The error code for this specific error. Defaults to 0.
     * @param Exception $previous The previous exception that caused this exception.
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
     
    /**
     * Canonocalizes ProtectedIndexException for easier human readable output.
     *
     * @return string The error code and error message concatenated into a string.
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
