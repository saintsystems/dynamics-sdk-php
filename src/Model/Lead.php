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
    * Gets the emailaddress1
    *
    * @return string The emailaddress1
    */
    public function get_fullname()
    {
        return $this->properties["firstname"].' '.$this->properties["lastname"];
    }

}