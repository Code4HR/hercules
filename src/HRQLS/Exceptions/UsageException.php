<?php
/**
 * Defines a custom exception thrown by the HRQLS framework.
 *
 * @package HRQLS\Exceptions
 */
 
namespace HRQLS\Exceptions;
 
 /**
  * Defines the custom UsageException class.
  * This exception should be thrown when a user tries to use a function incorrectly.
  * An example of this is trying to index an empty document, cause that makes no sense...how would you search for it?
  */
class UsageException extends \Exception
{
    /**
     * Constructs a new UsageException object.
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
