<?php

namespace Microsoft\Dynamics\Http;

use ArrayAccess;
use Microsoft\Dynamics\Model\Entity;

/**
 * The type EntityRequestBuilder.
 */
class EntityRequestBuilder extends BaseRequestBuilder implements IEntityRequestBuilder, ArrayAccess
{
    /**
     * Constructs a new EntityRequestBuilder.
     * @param string      $requestUrl The URL for the built request.
     * @param IBaseClient $client     The IBaseClient for handling requests.
     */
    public function __construct($requestUrl, IBaseClient $client, $returnType)
    {
        parent::__construct($requestUrl, $client, $returnType);
    }

    private $entityId;

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->entityId == $offset;
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $this->entityId = $offset;
        return $this;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($offset == 'id') {
            $this->entityId = $value;
        }
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->entityId);
    }

    /**
     * Builds the request.
     * @return IEntityRequest The built request.
     */
    public function request() //:IEntityRequest
    {
        return $this->requestWithOptions([]);
    }

    /**
     * Builds the request.
     * @param  array $options The query and header options for the request.
     * @return IEntityRequest  The built request.
     */
    public function requestWithOptions($options)
    {
        $uri = $this->requestUrl . (empty($this->entityId) ?: '('.$this->entityId.')');
        return new EntityRequest($uri, $this->client, $this->returnType, $options);
    }

}