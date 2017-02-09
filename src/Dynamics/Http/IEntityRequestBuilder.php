<?php

namespace Microsoft\Dynamics\Http;

interface IEntityRequestBuilder
{
    /**
     * Builds the request.
     * @return IEntityRequest The built request.
     */
    public function request();

    /**
     * Builds the request.
     * @param  array $options The query and header options for the request.
     * @return IEntityRequest  The built request.
     */
    public function requestWithOptions(array $options);
}
