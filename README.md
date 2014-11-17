Core – REST API tools
====

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/caffeina-core/api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/caffeina-core/api/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/caffeina-core/api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/caffeina-core/api/build-status/master)
[![Total Downloads](https://poser.pugx.org/caffeina-core/api/downloads.svg)](https://packagist.org/packages/caffeina-core/api)
[![Latest Stable Version](https://poser.pugx.org/caffeina-core/api/v/stable.svg)](https://packagist.org/packages/caffeina-core/api)
[![Latest Unstable Version](https://poser.pugx.org/caffeina-core/api/v/unstable.svg)](https://packagist.org/packages/caffeina-core/api)
[![License](https://poser.pugx.org/caffeina-core/api/license.svg)](https://packagist.org/packages/caffeina-core/api)


## Installation

Add package to your **composer.json**:

```json
{
  "require": {
    "caffeina-core/api": "~1"
  }
}
```


## Config Example

You can find the complete example in `config.sample.php`:

```php
<?php
return [
  'base' => [
    'endpoints' => APP_DIR.'/endpoints',
    'api_version' => ['v1','v2','beta'],
  ],
  'database' => [
    'enable'    => true,
    'name'      => 'my_awesome_api',
    'host'      => 'localhost',
    'port'      => 3306,
    'user'      => 'root',
    'password'  => 'root',
  ],
  'extra' => [
    'timezone'  => 'Europe/Rome',
  ],
];
```
## Endpoints Structure

Endpoints definition will be placed in `base.endpoints` directory. Every API namespace must be located there as a directory containing the route definitions files.

Example :

```
/endpoints
          /v1/api.php
          /v2/api.php
          /beta
                /api.php
                /api-new.php
```

## Event hooks

| Hook | Event Name | Parameters |
|------|------------|------------|
| `api.run`    | API server started                                    | |
| `api.error`  | API::error function triggered                         | $message,$status |
| `api.before` | Called when API namespace route group opens.          | $API_NAMESPACE |
| `api.after`  | Called before the API namespace route group closing.  | $API_NAMESPACE |


