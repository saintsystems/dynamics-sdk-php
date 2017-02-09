<?php

namespace Microsoft\OData;

class ODataClient implements IODataClient
{

    /**
     * Begin a fluent query against an odata service
     *
     * @param  string  $entitySet
     * @return \Microsoft\OData\QueryBuilder
     */
    public function entitySet($entitySet)
    {
        return $this->query()->from($entitySet);
    }

    /**
     * Get a new query builder instance.
     *
     * @return \Microsoft\OData\QueryBuilder
     */
    public function query()
    {
        return new QueryBuilder(
            $this, $this->getQueryGrammar(), $this->getPostProcessor()
        );
    }
}


