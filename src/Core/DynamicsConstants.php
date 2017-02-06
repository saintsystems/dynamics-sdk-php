<?php 
/**
* Copyright (c) Saint Systems, LLC.  All Rights Reserved.  
* Licensed under the MIT License.  See License in the project root 
* for license information.
* 
* Constants File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Dynamics
* @copyright 2017 Saint Systems, LLC
* @license   https://opensource.org/licenses/MIT MIT License
* @version   GIT: 0.1.0
* @link      https://www.microsoft.com/en-us/dynamics365/
*/

namespace Microsoft\Dynamics\Core;

class DynamicsConstants
{
    // Global Discovery Endpoint: https://msdn.microsoft.com/en-us/library/mt607485.aspx
    const REST_GLOBAL_DISCOVERY_ENDPOINT = "https://globaldisco.crm.dynamics.com/api/discovery/v1.0/Instances";
    const REST_INSTANCE_DISCOVERY_ENDPOINT_FORMAT = "https://{instance_url}/api/discovery/";

    // ODATA Versions to be used when accessing the Web API (see: https://msdn.microsoft.com/en-us/library/gg334391.aspx)
    const MAX_ODATA_VERSION_HEADER = "OData-MaxVersion";
    const ODATA_VERSION_HEADER = "OData-Version";

    const MAX_ODATA_VERSION = "4.0";
    const ODATA_VERSION = "4.0";

    const ODATA_MAX_PAGE_SIZE_HEADER = "Prefer";
    const ODATA_MAX_PAGE_SIZE_DEFAULT = "odata.maxpagesize=25";

    // Dynamics Online 2016 Update 1 or later (Dynamics 365)
    const MIN_API_VERSION = "v8.0";

    // These can be overwritten in setters in the Dynamics object
    const API_VERSION = "v8.2";

    // Dynamics Web API base endpoint format
    const REST_INSTANCE_ENDPOINT_FORMAT = "{scheme}://{instance_url}/api/data/";

    // Header to be used when impersonating another Dynamics user
    const REST_IMPERSONATION_HEADER = "MSCRMCallerID";

    // Define HTTP request constants
    const SDK_VERSION = "0.1.0";

    // Define error constants
    const MAX_PAGE_SIZE = 999;
    const MAX_PAGE_SIZE_ERROR = "Page size must be less than " . self::MAX_PAGE_SIZE;
    const TIMEOUT = "Timeout error";

    // Define error message constants
    const INSTANCE_URL_MISSING = "Instance URL cannot be null or empty.";
    const REQUEST_TIMED_OUT = "The request timed out.";
    const UNABLE_TO_CREATE_INSTANCE_OF_TYPE = "Unable to create instance of type.";

    // Define user error constants
    const INVALID_FILE = "Unable to open file stream for the given path.";
    const NO_ACCESS_TOKEN = "No access token has been provided.";
    const NO_APP_ID = "No app ID has been provided.";
    const NO_APP_SECRET = "No app secret has been provided.";

    // Define server error constants
    const UNABLE_TO_PARSE_RESPONSE = "The HTTP client sent back an invalid response";
}
