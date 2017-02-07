<?php
/**
* Copyright (c) Saint Systems, LLC.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* Lead File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Dynamics
* @copyright 2017 Saint Systems, LLC
* @license   https://opensource.org/licenses/MIT MIT License
* @version   GIT: 0.1.0
* @link     https://www.microsoft.com/en-us/dynamics365/
*/
namespace Microsoft\Dynamics\Model;

/**
* Lead class
*
* Prospect or potential sales opportunity. Leads are converted into accounts, contacts, 
* or opportunities when they are qualified. Otherwise, they are deleted or archived.
*
* @category  Model
* @package   Microsoft.Dynamics
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://www.microsoft.com/en-us/dynamics365/
*/
class Lead extends Entity
{
    /**
    * The array of properties available
    * to the model
    *
    * @var array(string => string)
    */
    //private $_propDict;
    
    /**
    * Construct a new Lead
    *
    * @param array $propDict A list of properties to set
    *
    * @return Lead
    */
    function __construct($propDict = array())
    {
        parent::__construct($propDict);
        //$this->_propDict = $propDict;
        return $this;
    }

    /**
    * Gets the property dictionary of the Lead
    *
    * @return array The list of properties
    */
    public function getProperties()
    {
        return $this->_propDict;
    }

    /**
    * Gets the firstname
    *
    * @return string The firstname
    */
    public function getFirstName()
    {
        if (array_key_exists("firstname", $this->_propDict)) {
            return $this->_propDict["firstname"];
        } else {
            return null;
        }
    }

    /**
    * Sets the firstname
    *
    * @param string $val The firstname
    *
    * @return Lead
    */
    public function setFirstName($val)
    {
        $this->_propDict["firstname"] = $val;
        return $this;
    }

    /**
    * Gets the lastname
    *
    * @return string The lastname
    */
    public function getLastName()
    {
        if (array_key_exists("lastname", $this->_propDict)) {
            return $this->_propDict["lastname"];
        } else {
            return null;
        }
    }

    /**
    * Sets the lastname
    *
    * @param string $val The lastname
    *
    * @return Lead
    */
    public function setLastName($val)
    {
        $this->_propDict["lastname"] = $val;
        return $this;
    }

    /**
    * Gets the emailaddress1
    *
    * @return string The emailaddress1
    */
    public function getEmailAddress1()
    {
        if (array_key_exists("emailaddress1", $this->_propDict)) {
            return $this->_propDict["emailaddress1"];
        } else {
            return null;
        }
    }

    /**
    * Sets the emailaddress1
    *
    * @param string $val The emailaddress1
    *
    * @return Lead
    */
    public function setEmailAddress1($val)
    {
        $this->_propDict["emailaddress1"] = $val;
        return $this;
    }

}