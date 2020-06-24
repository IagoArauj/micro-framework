<?php

namespace Core\Exceptions;

class CallbackFunctionException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {

        // Check if all is correctly initializated
        parent::__construct($message, $code, $previous);
    }

    
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}