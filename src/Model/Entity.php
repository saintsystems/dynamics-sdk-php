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

/**
* Entity class
*
* @category  Model
* @package   Microsoft.Dynamics
* @copyright 2016 Saint Systems, LLC
* @license   https://opensource.org/licenses/MIT MIT License
* @version   Release: 0.1.0
* @link     https://www.microsoft.com/en-us/dynamics365/
*/
class Entity
{
    /**
    * The array of properties available
    * to the model
    *
    * @var array(string => string)
    */
    protected $_propDict;
    
    /**
    * Construct a new Entity
    *
    * @param array $propDict A list of properties to set
    *
    * @return Entity
    */
    function __construct($propDict = array())
    {
        $this->_propDict = $propDict;
        return $this;
    }

    /**
    * Gets the property dictionary of the Entity
    *
    * @return array The list of properties
    */
    public function getProperties()
    {
        return $this->_propDict;
    }

    /**
    * Gets the id
    *
    * @return string The id
    */
    public function getId()
    {
        $classNameParts = explode('\\',get_class($this));
        $className = strtolower(end($classNameParts));

        $id = $className . "id";

        if (array_key_exists($id, $this->_propDict)) {
            return $this->_propDict[$id];
        } else if(array_key_exists("id", $this->_propDict)) {
            return $this->_propDict["id"];
        } else {
            return null;
        }
    }

    /**
    * Sets the id
    *
    * @param string $val The id
    *
    * @return Entity
    */
    public function setId($val)
    {
        $this->_propDict["id"] = $val;
        return $this;
    }
}
