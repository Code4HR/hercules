<?php
/**
 * Defines a custom exception thrown by the HRQLS framework.
 *
 * @package HRQLS\Exceptions
 */
 
namespace HRQLS\Exceptions;
 
 /**
  * Defines the custom FailedRequestException class.
  * This exception should be thrown when an request to a API Request fails.
  * It is up to the users to determine when a request failed, but a common occurence is when StatusCode != 200.
  */
class FailedRequestException extends \Exception
{
    /**
     * Constructs a new FailedRequestException object.
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
     * Canonocalizes UsageException for easier human readable output.
     *
     * @return string The error code and error message concatenated into a string.
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
