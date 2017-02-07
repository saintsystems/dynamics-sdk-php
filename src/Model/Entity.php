<?php
/**
* Copyright (c) Saint Systems, LLC.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* Entity File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Dynamics
* @copyright 2017 Saint Systems, LLC
* @license   https://opensource.org/licenses/MIT MIT License
* @version   GIT: 0.1.0
* @link      https://www.microsoft.com/en-us/dynamics365/
*/
namespace Microsoft\Dynamics\Model;

use ArrayAccess;
//use Microsoft\Dynamics\Support;

/**
* Entity class
*
* @category  Model
* @package   Microsoft.Dynamics
* @copyright 2017 Saint Systems, LLC
* @license   https://opensource.org/licenses/MIT MIT License
* @version   Release: 0.1.0
* @link     https://www.microsoft.com/en-us/dynamics365/
*/
class Entity implements ArrayAccess
{
    /**
     * The primary key for the entity.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
    * The array of properties available
    * to the model
    *
    * @var array(string => string)
    */
    protected $properties;

    /**
     * The loaded relationships for the model.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * The properties that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];
    
    /**
    * Construct a new Entity
    *
    * @param array $properties A list of properties to set
    *
    * @return Entity
    */
    function __construct($properties = array())
    {
        $classNameParts = explode('\\',get_class($this));
        $className = strtolower(end($classNameParts));
        $this->primaryKey = $className.$this->primaryKey;
        $this->properties = $properties;
        return $this;
    }

    /**
    * Gets the property dictionary of the Entity
    *
    * @return array The list of properties
    */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Dynamically retrieve properties on the entity.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getProperty($key);
    }

    /**
     * Dynamically set properties on the entity.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setProperty($key, $value);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
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
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    /**
     * Determine if a property or relation exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return ! is_null($this->getProperty($key));
    }

    /**
     * Unset a property on the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->properties[$key], $this->relations[$key]);
    }

    /**
     * Determine whether a property should be cast to a native type.
     *
     * @param  string  $key
     * @param  array|string|null  $types
     * @return bool
     */
    public function hasCast($key, $types = null)
    {
        if (array_key_exists($key, $this->getCasts())) {
            return $types ? in_array($this->getCastType($key), (array) $types, true) : true;
        }

        return false;
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        // if ($this->getIncrementing()) {
        //     return array_merge([
        //         $this->getKeyName() => $this->keyType,
        //     ], $this->casts);
        // }

        return $this->casts;
    }

    /**
     * Determine whether a value is Date / DateTime castable for inbound manipulation.
     *
     * @param  string  $key
     * @return bool
     */
    protected function isDateCastable($key)
    {
        return $this->hasCast($key, ['date', 'datetime']);
    }

    /**
     * Determine whether a value is JSON castable for inbound manipulation.
     *
     * @param  string  $key
     * @return bool
     */
    protected function isJsonCastable($key)
    {
        return $this->hasCast($key, ['array', 'json', 'object', 'collection']);
    }

    /**
     * Get the type of cast for a entity property.
     *
     * @param  string  $key
     * @return string
     */
    protected function getCastType($key)
    {
        return trim(strtolower($this->getCasts()[$key]));
    }

    /**
     * Cast a property to a native PHP type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function castProperty($key, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($this->getCastType($key)) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return $this->fromJson($value, true);
            case 'array':
            case 'json':
                return $this->fromJson($value);
            //case 'collection':
                //return new BaseCollection($this->fromJson($value));
            case 'date':
            case 'datetime':
                return $this->asDateTime($value);
            case 'timestamp':
                return $this->asTimeStamp($value);
            default:
                return $value;
        }
    }

    /**
     * Set a given property on the entity.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setProperty($key, $value)
    {
        // First we will check for the presence of a mutator for the set operation
        // which simply lets the developers tweak the property as it is set on
        // the entity, such as "json_encoding" a listing of data for storage.
        if ($this->hasSetMutator($key)) {
            //$method = 'set'.Str::studly($key).'Property';
            $method = 'set_'.$key;

            return $this->{$method}($value);
        }

        // // If an attribute is listed as a "date", we'll convert it from a DateTime
        // // instance into a form proper for storage on the database tables using
        // // the connection grammar's date format. We will auto set the values.
        // elseif ($value && (in_array($key, $this->getDates()) || $this->isDateCastable($key))) {
        //     $value = $this->fromDateTime($value);
        // }

        // if ($this->isJsonCastable($key) && ! is_null($value)) {
        //     $value = $this->asJson($value);
        // }

        // // If this attribute contains a JSON ->, we'll set the proper value in the
        // // attribute's underlying array. This takes care of properly nesting an
        // // attribute in the array's value in the case of deeply nested items.
        // if (Str::contains($key, '->')) {
        //     return $this->fillJsonAttribute($key, $value);
        // }

        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * Determine if a set mutator exists for a property.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasSetMutator($key)
    {
        // return method_exists($this, 'set'.Str::studly($key).'Attribute');
        return method_exists($this, 'set_'.$key);
    }

    /**
     * Get a property from the entity.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getProperty($key)
    {
        if (! $key) {
            return;
        }

        if ($key === 'id') {
            $key = $this->primaryKey;
        }

        if (array_key_exists($key, $this->properties) || $this->hasGetMutator($key)) {
            return $this->getPropertyValue($key);
        }

        if (method_exists(self::class, $key)) {
            return;
        }

        return $this->getRelationValue($key);
    }

    /**
     * Get a plain property (not a relationship).
     *
     * @param  string  $key
     * @return mixed
     */
    public function getPropertyValue($key)
    {
        $value = $this->getPropertyFromArray($key);

        // If the property has a get mutator, we will call that then return what
        // it returns as the value, which is useful for transforming values on
        // retrieval from the model to a form that is more useful for usage.
        if ($this->hasGetMutator($key)) {
            return $this->mutateProperty($key, $value);
        }

        // If the property exists within the cast array, we will convert it to
        // an appropriate native PHP type dependant upon the associated value
        // given with the key in the pair. Dayle made this comment line up.
        if ($this->hasCast($key)) {
            return $this->castProperty($key, $value);
        }

        // If the property is listed as a date, we will convert it to a DateTime
        // instance on retrieval, which makes it quite convenient to work with
        // date fields without having to create a mutator for each property.
        // if (in_array($key, $this->getDates()) && ! is_null($value)) {
        //     return $this->asDateTime($value);
        // }

        return $value;
    }

    /**
     * Get a property from the $properties array.
     *
     * @param  string  $key
     * @return mixed
     */
    protected function getPropertyFromArray($key)
    {
        if (array_key_exists($key, $this->properties)) {
            return $this->properties[$key];
        }
    }

    /**
     * Determine if a get mutator exists for a property.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasGetMutator($key)
    {
        //return method_exists($this, 'get'.Str::studly($key).'Attribute');
        return method_exists($this, 'get_'.$key);
    }

    /**
     * Get the value of a property using its mutator.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function mutateProperty($key, $value)
    {
        //return $this->{'get'.Str::studly($key).'Attribute'}($value);
        return $this->{'get_'.$key}($value);
    }

    /**
     * Get the value of a property using its mutator for array conversion.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function mutatePropertyForArray($key, $value)
    {
        $value = $this->mutateProperty($key, $value);

        return $value instanceof Arrayable ? $value->toArray() : $value;
    }
}
