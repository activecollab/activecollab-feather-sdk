# Active Collab 5 SDK

This is a simple PHP library that makes communication with [Active Collab API](https://labs.activecollab.com/nightly-activecollab-api/v1/authentication.html) easy.

## Installation

If you choose to install this application with Composer instead of pulling down the git repository you will need to add a composer.json file to the location you would like to pull the repository down to featuring:

```json
{
    "require": {
        "activecollab/activecollab-feather-sdk": "^2.0"
    }
}
```
    
Run a ``composer update`` to install the package.

## First Connection

In order to connect, you will need API URL and API token. 
The `MY-API-URL` is the activeCollab base url without `/api/` or `api.php` suffix.

For every API call `API::setKey()` must be called.
Create an API key for your application by calling `API::issueToken()` once:

```php
<?php

  require_once 'vendor/autoload.php';

  use \ActiveCollab\SDK\Client as API;

  API::setUrl('MY-API-URL');
  
  // Use issueToken() method to get a new token. Store it for later use
  try {
    $token = API::issueToken('my@email.com', 'MY-PASSWORD', 'NAME-OF-MY-APP', 'NAME-OF-MY-COMPANY');
  } catch (Exception $e) {
    die($e->getMessage());
  }
  
  // Set token before making calls
  API::setKey($token);

  print '<pre>';
  print_r(API::info());
  print '</pre>';
```

This example will contact activeCollab and ask for application and user info. Response is a simple associative array with a lot of details about the system that you are communicating with.

## Making API Calls

Listing all tasks in project #65 is easy. Just call:

```php
API::get('projects/65/tasks');
```

To create a task, simply send a POST request:

```php
try {
  API::post('projects/65/tasks', [
    'name' => 'This is a task name',
    'assignee_id' => 48
  ]);
} catch(AppException $e) {
  print $e->getMessage() . '<br><br>';
  // var_dump($e->getServerResponse()); (need more info?)
}
```

To update a task, PUT request will be needed:

```php
try {
  API::put('projects/65/tasks/123', [
    'name' => 'Updated named'
  ]);
} catch(AppException $e) {
  print $e->getMessage() . '<br><br>';
  // var_dump($e->getServerResponse()); (need more info?)
}
```

``post()`` and ``put()`` methods can take two arguments:

1. ``command`` (required) - API command,
3. ``variables`` - array of request variables (payload)

To remove a task, call:

```php
try {
  API::delete('projects/65/tasks/123');
} catch(AppException $e) {
  print $e->getMessage() . '<br><br>';
  // var_dump($e->getServerResponse()); (need more info?)
}
```

``delete()`` method only requires ``command`` argument to be provided.

For full list of available API command, please check [Active Collab API documentation](https://labs.activecollab.com/nightly-activecollab-api/).
