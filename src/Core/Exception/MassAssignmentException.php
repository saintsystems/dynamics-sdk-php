<?php

namespace Microsoft\Core\Exception;

class MassAssignmentException extends ApplicationException
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
