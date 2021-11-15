# Get started with the Saint Systems Microsoft Dynamics 365 SDK for PHP

*This SDK is currently in preview. Please continue to provide [feedback](https://github.com/saintsystems/dynamics-sdk-php/issues/new) as we iterate towards a production-supported library.*

[![Build Status](https://travis-ci.org/saintsystems/dynamics-sdk-php.svg?branch=master)](https://travis-ci.org/saintsystems/dynamics-sdk-php)

For WordPress users, please see our [Gravity Forms Dynamics 365 Add-On](https://www.saintsystems.com/products/gravity-forms-dynamics-crm-add-on/).

## Install the SDK
You can install the PHP SDK with Composer.
```
{
    "require": {
        "Microsoft/Dynamics": "0.1.*"
    }
}
```
## Get started with Microsoft Dynamics 365

### Register your application

Register your application to use the Microsoft Dynamics 365 API by using one of the following
supported authentication portals:

* [Microsoft Azure Active Directory](https://manage.windowsazure.com): Register
  a new application in your tenant's Active Directory to support work or school
  users for your tenant, or multiple tenants.
* [Microsoft Application Registration Portal](https://apps.dev.microsoft.com) (**Coming Soon**):
  Register a new application that authenticates using the v2.0 authentication endpoint. This endpoint authenticates both personal (Microsoft) and work or school (Azure Active Directory) accounts.

### Authenticate with the Microsoft Graph service

The Microsoft Dynamics 365 SDK for PHP does not include any default authentication implementations.
Instead, you can authenticate with the library of your choice.

When authenticating, you simply need to request access to your Dynamics 365 instance URL using the `resource` parameter of Azure AD.

### Call Microsoft Dynamics 365

The following is an example that shows how to call Microsoft Dynamics 365 Web API.

```php
use Microsoft\Dynamics\Dynamics;
use Microsoft\Dynamics\Model;

class UsageExample
{
    $instanceUrl = 'https://contoso.crm.dynamics.com';
    $accessToken = 'xxx';

    $dynamics = new Dynamics();
    $dynamics->setInstanceUrl($instanceUrl)
             ->setAccessToken($accessToken);

    $leads = $dynamics->createRequest("GET", "/leads")
                  ->setReturnType(Model\Lead::class)
                  ->execute();

    $lead = $leads[0];

    echo "Hello, I am $lead->getFirstName() ";

    // OR GET a specific lead by ID

    $lead = $dynamics->createRequest("GET", "/leads(xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx)")
                  ->setReturnType(Model\Lead::class)
                  ->execute();

    echo "Hello, I am $lead->getFirstName() ";
}
```

## Develop

### Run Tests

Run ```vendor/bin/phpunit``` from the base directory.


## Documentation and resources

* [Documentation](https://github.com/saintsystems/dynamics-sdk-php/blob/master/docs/index.html)

* [Wiki](https://github.com/saintsystems/dynamics-sdk-php/wiki)

* [Examples](https://github.com/saintsystems/dynamics-sdk-php/wiki/Example-calls)

* [Microsoft Dynamics 365 website](https://www.microsoft.com/en-us/dynamics365)

* [Microsoft Dynamics 365 Web API Documentation](https://msdn.microsoft.com/library/mt593051.aspx#documentation)

## Issues

View or log issues on the [Issues](https://github.com/saintsystems/dynamics-sdk-php/issues) tab in the repo.

## Copyright and license

Copyright (c) Saint Systems, LLC. All Rights Reserved. Licensed under the MIT [license](LICENSE).
