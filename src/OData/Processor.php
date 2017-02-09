<?php

namespace Microsoft\OData;

class Processor
{
    /**
     * Process the results of a "select" query.
     *
     * @param  \Microsoft\OData\QueryBuilder  $query
     * @param  array  $results
     * @return array
     */
    public function processSelect(QueryBuilder $query, $results)
    {
        return $results;
    }

    /**
     * Process an "insert get ID" query.
     *
     * @param  \Microsoft\OData\QueryBuilder  $query
     * @param  string  $sql
     * @param  array   $values
     * @param  string  $sequence
     * @return int
     */
    public function processInsertGetId(QueryBuilder $query, $sql, $values, $sequence = null)
    {
        $query->getConnection()->insert($sql, $values);

        $id = $query->getConnection()->getPdo()->lastInsertId($sequence);

        return is_numeric($id) ? (int) $id : $id;
    }

    /**
     * Process the results of a column listing query.
     *
     * @param  array  $results
     * @return array
     */
    public function processColumnListing($results)
    {
        return $results;
    }
}
