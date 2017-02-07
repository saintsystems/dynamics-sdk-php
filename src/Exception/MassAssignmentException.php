<?php

namespace Microsoft\Dynamics\Exception;

class MassAssignmentException extends DynamicsException
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
