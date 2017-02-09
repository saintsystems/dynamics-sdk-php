<?php

namespace Microsoft\OData;

interface IODataClient
{
    /**
     * Gets the IAuthenticationProvider for authenticating HTTP requests.
     * @var \Microsoft\Core\Http\IAuthenticationProvider
     */
    public function getAuthenticationProvider();

    /**
     * Gets the base URL for requests of the client.
     * @var string
     */
    public function getBaseUrl();

    /**
     * Gets the IHttpProvider for sending HTTP requests.
     * @var IHttpProvider
     */
    public function getHttpProvider();

    /**
     * Begin a fluent query against an OData service
     *
     * @param  string  $entitySet
     * @return \Microsoft\OData\QueryBuilder
     */
    public function entitySet(string $entitySet);

    /**
     * Get a new query builder instance.
     *
     * @return \Microsoft\OData\QueryBuilder
     */
    public function query();
}
