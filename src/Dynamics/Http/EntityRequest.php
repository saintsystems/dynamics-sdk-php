<?php

namespace Microsoft\Dynamics\Http;

use Microsoft\Dynamics\Model\Entity;
use Microsoft\Dynamics\Http\QueryOption;

/// <summary>
/// The type EntityRequest.
/// </summary>
class EntityRequest extends BaseRequest// implements IEntityRequest
{
    /// <summary>
    /// Constructs a new EntityRequest.
    /// </summary>
    /// <param name="requestUrl">The URL for the built request.</param>
    /// <param name="client">The <see cref="IBaseClient"/> for handling requests.</param>
    /// <param name="options">Query and header option name value pairs for the request.</param>
    public function __construct(string $requestUrl, IBaseClient $client, string $returnType, array $options)
    {
        parent::__construct($requestUrl, $client, $returnType, $options);
    }

    /// <summary>
    /// Creates the specified Entity using POST.
    /// </summary>
    /// <param name="entityToCreate">The Entity to create.</param>
    /// <returns>The created Entity.</returns>
    public function create(Entity $entityToCreate) //:Entity
    {
        $this->contentType = 'application/json';
        $method = 'POST';
        $newEntity = $this->send($entityToCreate);
        $this->initializeCollectionProperties($newEntity);
        return $newEntity;
    }

    /// <summary>
    /// Deletes the specified Entity.
    /// </summary>
    /// <returns>The task to await.</returns>
    public function delete()
    {
        $this->method = 'DELETE';
        $this->send(null);
    }

    /// <summary>
    /// Gets the specified Entity.
    /// </summary>
    /// <returns>The Entity.</returns>
    public function get()
    {
        $this->method = 'GET';
        $retrievedEntity = $this->send(null);
        $this->initializeCollectionProperties($retrievedEntity);
        return $retrievedEntity;
    }

    /// <summary>
    /// Updates the specified Entity using PATCH.
    /// </summary>
    /// <param name="entityToUpdate">The Entity to update.</param>
    /// <returns>The updated Entity.</returns>
    public function update(Entity $entityToUpdate)
    {
        $this->contentType = 'application/json';
        $this->method = 'PATCH';
        $updatedEntity = $this->send($entityToUpdate);
        $this->initializeCollectionProperties($updatedEntity);
        return $updatedEntity;
    }

    /// <summary>
    /// Adds the specified expand value to the request.
    /// </summary>
    /// <param name="value">The expand value.</param>
    /// <returns>The request object to send.</returns>
    public function expand(string $value) //:IEntityRequest
    {
        $this->queryOptions[] = new QueryOption('$expand', $value);
        return $this;
    }

    /// <summary>
    /// Adds the specified expand value to the request.
    /// </summary>
    /// <param name="expandExpression">The expression from which to calculate the expand value.</param>
    /// <returns>The request object to send.</returns>
    // public IEntityRequest Expand(Expression<Func<Entity, object>> expandExpression)
    // {
       //  if (expandExpression == null)
    //     {
    //         throw new ArgumentNullException(nameof(expandExpression));
    //     }
    //     string error;
    //     string value = ExpressionExtractHelper.ExtractMembers(expandExpression, out error);
    //     if (value == null)
    //     {
    //         throw new ArgumentException(error, nameof(expandExpression));
    //     }
    //     else
    //     {
    //         this.QueryOptions.Add(new QueryOption("$expand", value));
    //     }
    //     return this;
    // }

    /// <summary>
    /// Adds the specified select value to the request.
    /// </summary>
    /// <param name="value">The select value.</param>
    /// <returns>The request object to send.</returns>
    public function select(string $value) //:IEntityRequest
    {
        $this->queryOptions[] = new QueryOption('$select', $value);
        return $this;
    }

    /// <summary>
    /// Adds the specified select value to the request.
    /// </summary>
    /// <param name="selectExpression">The expression from which to calculate the select value.</param>
    /// <returns>The request object to send.</returns>
    // public IEntityRequest Select(Expression<Func<Entity, object>> selectExpression)
    // {
    //     if (selectExpression == null)
    //     {
    //         throw new ArgumentNullException(nameof(selectExpression));
    //     }
    //     string error;
    //     string value = ExpressionExtractHelper.ExtractMembers(selectExpression, out error);
    //     if (value == null)
    //     {
    //         throw new ArgumentException(error, nameof(selectExpression));
    //     }
    //     else
    //     {
    //         this.QueryOptions.Add(new QueryOption("$select", value));
    //     }
    //     return this;
    // }

    /// <summary>
    /// Initializes any collection properties after deserialization, like next requests for paging.
    /// </summary>
    /// <param name="entityToInitialize">The <see cref="Entity"/> with the collection properties to initialize.</param>
    private function initializeCollectionProperties($entityToInitialize)
    {

    }
}
