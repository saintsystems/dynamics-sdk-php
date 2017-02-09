<?php

namespace Microsoft\OData;

use Closure;
use RuntimeException;
use BadMethodCallException;

class QueryBuilder
{
    /**
     * Gets the IBaseClient for handling requests.
     * @var IBaseClient
     */
    public $client;

    /**
     * Gets the URL for the built request, without query string.
     * @var string
     */
    public $requestUrl;

    /**
     * Gets the URL for the built request, without query string.
     * @var object
     */
    public $returnType;

    /**
     * The current query value bindings.
     *
     * @var array
     */
    public $bindings = [
        'select' => [],
        'where'  => [],
        'order'  => [],
    ];

    /**
     * The entity set which the query is targeting.
     *
     * @var string
     */
    public $entitySet;

    /**
     * The entity key of the entity set which the query is targeting.
     *
     * @var string
     */
    public $entityKey;

    /**
     * An aggregate function to be run.
     *
     * @var array
     */
    public $count;

    /**
     * The specific set of properties to return for this entity or complex type
     * http://docs.oasis-open.org/odata/odata/v4.0/errata03/os/complete/part2-url-conventions/odata-v4.0-errata03-os-part2-url-conventions-complete.html#_Toc453752360
     *
     * @var array
     */
    public $select;

    /**
     * The where constraints for the query.
     *
     * @var array
     */
    public $wheres;

    /**
     * The groupings for the query.
     *
     * @var array
     */
    public $groups;

    /**
     * The orderings for the query.
     *
     * @var array
     */
    public $orders;

    /**
     * The maximum number of records to return.
     *
     * @var int
     */
    public $limit;

    /**
     * The number of records to skip.
     *
     * @var int
     */
    public $offset;

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    public $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'like binary', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to', 'not ilike', '~~*', '!~~*',
    ];

    /**
     * Constructs a new BaseRequestBuilder.
     * @param string      $requestUrl The URL for the built request.
     * @param IBaseClient $client     The IBaseClient for handling requests.
     */
    public function __construct(string $requestUrl, 
                                IBaseClient $client, 
                                string $returnType)
    {
        $this->client = $client;
        $this->requestUrl = $requestUrl;
        $this->returnType = $returnType;
    }

    /**
     * Set the columns to be selected.
     *
     * @param  array|mixed  $columns
     * @return $this
     */
    public function select($select = [])
    {
        $this->select = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    /**
     * Add a new properties to the $select query option.
     *
     * @param  array|mixed  $select
     * @return $this
     */
    public function addSelect($select)
    {
        $select = is_array($select) ? $select : func_get_args();

        $this->select = array_merge((array) $this->select, $select);

        return $this;
    }

    /**
     * Set the entity set which the query is targeting.
     *
     * @param  string  $entitySet
     * @return $this
     */
    public function from($entitySet)
    {
        $this->from = $entitySet;

        return $this;
    }

    /**
     * Apply the callback's query changes if the given "value" is true.
     *
     * @param  bool  $value
     * @param  \Closure  $callback
     * @param  \Closure  $default
     * @return \Microsoft\Dynamics\QueryBuilder
     */
    public function when($value, $callback, $default = null)
    {
        $builder = $this;

        if ($value) {
            $builder = call_user_func($callback, $builder);
        } elseif ($default) {
            $builder = call_user_func($default, $builder);
        }

        return $builder;
    }

    /**
     * Merge an array of where clauses and bindings.
     *
     * @param  array  $wheres
     * @param  array  $bindings
     * @return void
     */
    public function mergeWheres($wheres, $bindings)
    {
        $this->wheres = array_merge((array) $this->wheres, (array) $wheres);

        $this->bindings['where'] = array_values(
            array_merge($this->bindings['where'], (array) $bindings)
        );
    }



}
