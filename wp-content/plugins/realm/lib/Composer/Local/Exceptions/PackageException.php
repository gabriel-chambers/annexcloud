<?php

namespace E25m\Realm\Composer\Local\Exceptions;

use \Exception;

class PackageException extends Exception
{
    /**
     * __construct
     *
     * @param String $message
     * @param int $code
     * @param Exception $previous
     * @return void
     */
    public function __construct(String $message, int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * __toString
     *
     * @return String
     */
    public function __toString(): String
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
