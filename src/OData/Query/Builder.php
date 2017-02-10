<?php

namespace SaintSystems\OData\Query;

use Closure;
use RuntimeException;
use BadMethodCallException;
use Illuminate\Support\Arr;
use SaintSystems\OData\IODataClient;

class Builder
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
    public $properties;

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
    public $take;

    /**
     * The number of records to skip.
     *
     * @var int
     */
    public $skip;

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

    // /**
    //  * Constructs a new BaseRequestBuilder.
    //  * @param string      $requestUrl The URL for the built request.
    //  * @param IBaseClient $client     The IBaseClient for handling requests.
    //  */
    // public function __construct(string $requestUrl, 
    //                             IBaseClient $client, 
    //                             string $returnType)
    // {
    //     $this->client = $client;
    //     $this->requestUrl = $requestUrl;
    //     $this->returnType = $returnType;
    // }
    /**
     * Create a new query builder instance.
     *
     * @param  \SaintSystems\OData\IODataClient  $client
     * @param  \SaintSystems\OData\Grammar  $grammar
     * @param  \SaintSystems\OData\Processor  $processor
     * @return void
     */
    public function __construct(IODataClient $client,
                                Grammar $grammar = null,
                                Processor $processor = null)
    {
        $this->client = $client;
        $this->grammar = $grammar ?: $client->getQueryGrammar();
        $this->processor = $processor ?: $client->getPostProcessor();
    }

    /**
     * Set the properties to be selected.
     *
     * @param  array|mixed  $properties
     * @return $this
     */
    public function select($properties = [])
    {
        $this->properties = is_array($properties) ? $properties : func_get_args();

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
    public function entitySet(string $entitySet)
    {
        $this->entitySet = $entitySet;

        return $this;
    }

    /**
     * Set the entity set which the query is targeting.
     *
     * @param  string  $entityKey
     * @return $this
     */
    public function entityKey(string $entityKey)
    {
        $this->entityKey = $entityKey;

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

    /**
     * Get the HTTP Request representation of the query.
     *
     * @return string
     */
    public function toRequest()
    {
        return $this->grammar->compileSelect($this);
    }

    /**
     * Execute a query for a single record by ID.
     *
     * @param  int    $id
     * @param  array  $properties
     * @return mixed|static
     */
    public function find($id, $properties = [])
    {
        return $this->entityKey($id)->first($properties);
    }

    /**
     * Get a single column's value from the first result of a query.
     *
     * @param  string  $column
     * @return mixed
     */
    public function value($column)
    {
        $result = (array) $this->first([$column]);

        return count($result) > 0 ? reset($result) : null;
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  array   $columns
     * @return \stdClass|array|null
     */
    public function first($properties = [])
    {
        return $this->take(1)->get($properties)->first();
    }

    /**
     * Set the "$skip" value of the query.
     *
     * @param  int  $value
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function skip($value)
    {
        return $this->skip = $value;
    }

    /**
     * Set the "$top" value of the query.
     *
     * @param  int  $value
     * @return \SaintSystems\OData\QueryBuilder|static
     */
    public function take($value)
    {
        $this->take = $value;
        return $this;
    }

    /**
     * Execute the query as a "GET" request.
     *
     * @param  array  $properties
     * @return \Illuminate\Support\Collection
     */
    public function get($properties = [])
    {
        $original = $this->properties;

        if (is_null($original)) {
            $this->properties = $properties;
        }

        $results = $this->processor->processSelect($this, $this->runGet());

        $this->properties = $original;

        //return collect($results);
        return $results;
    }

    /**
     * Run the query as a "GET" request against the client.
     *
     * @return array
     */
    protected function runGet()
    {
        return $this->client->get(
            $this->grammar->compileSelect($this), $this->getBindings()
        );
    }

    /**
     * Get a new instance of the query builder.
     *
     * @return \SaintSystems\OData\QueryBuilder
     */
    public function newQuery()
    {
        return new static($this->client, $this->grammar, $this->processor);
    }

    /**
     * Get the current query value bindings in a flattened array.
     *
     * @return array
     */
    public function getBindings()
    {
        return Arr::flatten($this->bindings);
    }

}
