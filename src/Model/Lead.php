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
    // protected $fillable = [
    //     'address1_city',
    //     'leadid',
    //     'firstname',
    //     'lastname',
    //     'emailaddress1',
    // ];
    
    // protected $casts = [
    //     'address1_addresstypecode'    => 'integer',
    //     'address1_latitude'           => 'double',
    //     'address1_longitude'          => 'double',
    //     'address1_shippingmethodcode' => 'integer',
    //     'address1_utcoffset'          => 'integer',
    //     'address2_addresstypecode'    => 'integer',
    //     'address2_latitude'           => 'double',
    //     'address2_longitude'          => 'double',
    //     'address2_shippingmethodcode' => 'integer',
    //     'address2_utcoffset'          => 'integer',
    //     'budgetamount'                => 'float',
    //     'budgetamount_base'           => 'float',
    //     'budgetstatus'                => 'integer',
    //     'confirminterest'             => 'boolean',
    //     'new_donationamount'          => 'float',
    //     // 'createdon'                   => 'timestamp'
    // ];
    

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