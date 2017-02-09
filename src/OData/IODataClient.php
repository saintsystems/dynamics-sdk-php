<?php

namespace Microsoft\OData;

interface IODataClient
{
    /**
     * Begin a fluent query against an odata service
     *
     * @param  string  $entitySet
     * @return \Microsoft\OData\QueryBuilder
     */
    public function entitySet($entitySet);

    /**
     * Get a new query builder instance.
     *
     * @return \Microsoft\OData\QueryBuilder
     */
    public function query();
}
