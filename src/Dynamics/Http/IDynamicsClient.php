<?php

namespace Microsoft\Dynamics\Http;

/**
 * The interface IDynamicsClient.
 */
interface IDynamicsClient extends IBaseClient
{
    public function leads($id = null);
}
