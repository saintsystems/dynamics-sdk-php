<?php

namespace SaintSystems\OData\Query;

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
     * The components that make up an OData Request.
     *
     * @var array
     */
    protected $selectComponents = [
        'entitySet',
        'entityKey',
        'count',
        'properties',
        'wheres',
        //'expand',
        //'search',
        'orders',
        'take',
        //'skip',
    ];

    /**
     * Compile a select query into OData Uri
     *
     * @param  \SaintSystems\OData\Query\Builder  $query
     * @return string
     */
    public function compileSelect(Builder $query)
    {
        // If the query does not have any properties set, we'll set the properties to the
        // [] character to just get all of the columns from the database. Then we
        // can build the query and concatenate all the pieces together as one.
        $original = $query->properties;

        if (is_null($query->properties)) {
            $query->properties = [];
        }

        // To compile the query, we'll spin through each component of the query and
        // see if that component exists. If it does we'll just call the compiler
        // function for the component which is responsible for making the SQL.
        $uri = trim($this->concatenate(
            $this->compileComponents($query))
        );

        $query->properties = $original;
        
        return $uri;
    }

    /**
     * Compile the components necessary for a select clause.
     *
     * @param  \SaintSystems\OData\Query\Builder  $query
     * @return array
     */
    protected function compileComponents(Builder $query)
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
     * Compile the "from" portion of the query.
     *
     * @param  \SaintSystems\OData\Query\Builder  $query
     * @param  string  $entitySet
     * @return string
     */
    protected function compileEntitySet(Builder $query, $entitySet)
    {
        return $entitySet;
    }

    /**
     * Compile the entity key portion of the query.
     *
     * @param  \SaintSystems\OData\Query\Builder  $query
     * @param  string  $entityKey
     * @return string
     */
    protected function compileEntityKey(Builder $query, $entityKey)
    {
        if (is_null($entityKey)) {
            return '';
        }

        $entityKey = $this->wrapKey($entityKey);

        return "($entityKey)";
    }

    protected function wrapKey($entityKey)
    {
        if (is_uuid($entityKey) || is_numeric($entityKey)) {
            return $entityKey;
        }
        return "'$entityKey'";;
    }

    /**
     * Compile an aggregated select clause.
     *
     * @param  \SaintSystems\OData\Query\Builder  $query
     * @param  array  $aggregate
     * @return string
     */
    protected function compileCount(Builder $query, $aggregate)
    {

        return '/$count';
    }

    /**
     * Compile the "$select=" portion of the OData query.
     *
     * @param  \SaintSystems\OData\Query\Builder  $query
     * @param  array  $properties
     * @return string|null
     */
    protected function compileProperties(Builder $query, $properties)
    {
        // If the query is actually performing an aggregating select, we will let that
        // compiler handle the building of the select clauses, as it will need some
        // more syntax that is best handled by that function to keep things neat.
        if (! is_null($query->count)) {
            return;
        }

        $select = '';
        if (! empty($properties)) {
            $select = '$select='.$this->columnize($properties);
        }
        
        return $select;
    }

    /**
     * Compile the "where" portions of the query.
     *
     * @param  \SaintSystems\OData\Query\Builder  $query
     * @return string
     */
    protected function compileWheres(Builder $query)
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
     * @param  \SaintSystems\OData\Query\Builder  $query
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
     * @param  \SaintSystems\OData\Query\Builder  $query
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
     * @param  \SaintSystems\OData\Query\Builder   $query
     * @param  array  $orders
     * @return string
     */
    protected function compileOrders(Builder $query, $orders)
    {
        if (! empty($orders)) {
            return '$orderby='.implode(',', $this->compileOrdersToArray($query, $orders));
        }

        return '';
    }

    /**
     * Compile the query orders to an array.
     *
     * @param  \SaintSystems\OData\Query\Builder 
     * @param  array  $orders
     * @return array
     */
    protected function compileOrdersToArray(Builder $query, $orders)
    {
        return array_map(function ($order) {
            return ! isset($order['sql'])
                        ? $order['column'].' '.$order['direction']
                        : $order['sql'];
        }, $orders);
    }

    /**
     * Compile the "$top" portions of the query.
     *
     * @param  \SaintSystems\OData\Query\Builder   $query
     * @param  int  $take
     * @return string
     */
    protected function compileTake(Builder $query, $take)
    {
        // If we have an entity key $top is redundant and invalid, so bail
        if (! empty($query->entityKey)) {
            return '';
        }
        return '$top='.(int) $take;
    }

    /**
     * Compile the "$skip" portions of the query.
     *
     * @param  \SaintSystems\OData\Query\Builder   $query
     * @param  int  $skip
     * @return string
     */
    protected function compileSkip(Builder $query, $skip)
    {
        return '$skip='.(int) $skip;
    }

    /**
     * Convert an array of property names into a delimited string.
     *
     * @param  array   $properties
     * @return string
     */
    public function columnize(array $properties)
    {
        return implode(',', $properties);
    }

    /**
     * Concatenate an array of segments, removing empties.
     *
     * @param  array   $segments
     * @return string
     */
    protected function concatenate($segments)
    {
        return implode('', array_filter($segments, function ($value) {
            return (string) $value !== '';
        }));
    }
}
