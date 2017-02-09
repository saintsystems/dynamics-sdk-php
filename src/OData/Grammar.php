<?php

namespace Microsoft\OData;

class Grammar
{
    /**
     * All of the available clause operators.
     *
     * @var array
     */
    protected $operators = [
        '=', '<', '>', '<=', '>=', '!<', '!>', '<>', '!=',
        'contains', 'startswith', 'endswith',
    ];

    /**
     * The components that make up a select clause.
     *
     * @var array
     */
    protected $selectComponents = [
        'entitySet',
        'entityKey'
        'count',
        'select',
        'wheres',
        //'expand',
        //'search',
        'orders',
        //'take',
        //'skip',
    ];

    /**
     * Compile a select query into OData Uri
     *
     * @param  \Microsoft\OData\QueryBuilder  $query
     * @return string
     */
    public function compileSelect(QueryBuilder $query)
    {
        // If the query does not have any select set, we'll set the $select to the
        // [] character to just get all of the columns from the database. Then we
        // can build the query and concatenate all the pieces together as one.
        $original = $query->select;

        if (is_null($query->select)) {
            $query->select = [];
        }

        // To compile the query, we'll spin through each component of the query and
        // see if that component exists. If it does we'll just call the compiler
        // function for the component which is responsible for making the SQL.
        $uri = trim($this->concatenate(
            $this->compileComponents($query))
        );

        $query->select = $original;

        return $uri;
    }

    /**
     * Compile the components necessary for a select clause.
     *
     * @param  \Microsoft\OData\QueryBuilder  $query
     * @return array
     */
    protected function compileComponents(QueryBuilder $query)
    {
        $uri = [];

        foreach ($this->selectComponents as $component) {
            // To compile the query, we'll spin through each component of the query and
            // see if that component exists. If it does we'll just call the compiler
            // function for the component which is responsible for making the SQL.
            if (! is_null($query->$component)) {
                $method = 'compile'.ucfirst($component);

                $uri[$component] = $this->$method($query, $query->$component);
            }
        }

        return $uri;
    }

    /**
     * Compile an aggregated select clause.
     *
     * @param  \Microsoft\OData\QueryBuilder  $query
     * @param  array  $aggregate
     * @return string
     */
    protected function compileCount(QueryBuilder $query, $aggregate)
    {

        return '/$count';
    }

    /**
     * Compile the "from" portion of the query.
     *
     * @param  \Microsoft\OData\QueryBuilder  $query
     * @param  string  $entitySet
     * @return string
     */
    protected function compileEntitySet(QueryBuilder $query, $entitySet)
    {
        return $this->entitySet;
    }

    /**
     * Compile the entity key portion of the query.
     *
     * @param  \Microsoft\OData\QueryBuilder  $query
     * @param  string  $entityKey
     * @return string
     */
    protected function compileEntityKey(QueryBuilder $query, $entityKey)
    {
        if (is_null($entityKey)) {
            return '';
        }

        return "($entityKey)";
    }

    /**
     * Compile the $select portion of the OData query.
     *
     * @param  \Microsoft\OData\QueryBuilder  $query
     * @param  array  $select
     * @return string|null
     */
    protected function compileSelect(QueryBuilder $query, $select)
    {
        // If the query is actually performing an aggregating select, we will let that
        // compiler handle the building of the select clauses, as it will need some
        // more syntax that is best handled by that function to keep things neat.
        if (! is_null($query->count)) {
            return;
        }

        $select = '$select=';

        return $select.$this->columnize($select);
    }

    /**
     * Compile the "where" portions of the query.
     *
     * @param  \Microsoft\OData\QueryBuilder  $query
     * @return string
     */
    protected function compileWheres(QueryBuilder $query)
    {
        // Each type of where clauses has its own compiler function which is responsible
        // for actually creating the where clauses SQL. This helps keep the code nice
        // and maintainable since each clause has a very small method that it uses.
        if (is_null($query->wheres)) {
            return '';
        }

        // If we actually have some where clauses, we will strip off the first boolean
        // operator, which is added by the query builders for convenience so we can
        // avoid checking for the first clauses in each of the compilers methods.
        if (count($sql = $this->compileWheresToArray($query)) > 0) {
            return $this->concatenateWhereClauses($query, $sql);
        }

        return '';
    }

    /**
     * Get an array of all the where clauses for the query.
     *
     * @param  \Microsoft\OData\QueryBuilder  $query
     * @return array
     */
    protected function compileWheresToArray($query)
    {
        return collect($query->wheres)->map(function ($where) use ($query) {
            return $where['boolean'].' '.$this->{"where{$where['type']}"}($query, $where);
        })->all();
    }

    /**
     * Format the where clause statements into one string.
     *
     * @param  \Microsoft\OData\QueryBuilder  $query
     * @param  array  $sql
     * @return string
     */
    protected function concatenateWhereClauses($query, $filter)
    {
        $conjunction = 'where';//$query instanceof JoinClause ? 'on' : 'where';

        return $conjunction.' '.$this->removeLeadingBoolean(implode(' ', $filter));
    }

    /**
     * Compile the "order by" portions of the query.
     *
     * @param  \Microsoft\OData\QueryBuilder   $query
     * @param  array  $orders
     * @return string
     */
    protected function compileOrders(QueryBuilder $query, $orders)
    {
        if (! empty($orders)) {
            return '$orderby='.implode(',', $this->compileOrdersToArray($query, $orders));
        }

        return '';
    }

    /**
     * Compile the query orders to an array.
     *
     * @param  \Microsoft\OData\QueryBuilder 
     * @param  array  $orders
     * @return array
     */
    protected function compileOrdersToArray(QueryBuilder $query, $orders)
    {
        return array_map(function ($order) {
            return ! isset($order['sql'])
                        ? $order['column'].' '.$order['direction']
                        : $order['sql'];
        }, $orders);
    }

    /**
     * Compile the "limit" portions of the query.
     *
     * @param  \Microsoft\OData\QueryBuilder   $query
     * @param  int  $limit
     * @return string
     */
    protected function compileLimit(QueryBuilder $query, $limit)
    {
        return '$top='.(int) $limit;
    }

    /**
     * Compile the "offset" portions of the query.
     *
     * @param  \Microsoft\OData\QueryBuilder   $query
     * @param  int  $offset
     * @return string
     */
    protected function compileOffset(QueryBuilder $query, $offset)
    {
        return '$skip='.(int) $offset;
    }
}
