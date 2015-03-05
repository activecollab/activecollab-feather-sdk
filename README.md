# activeCollab SDK

This is a simple PHP library that makes communication with [activeCollab API](https://www.activecollab.com/docs/manuals/developers/api) easy. 

## Installation

If you choose to install this application with Composer instead of pulling down the git repository you will need to add a composer.json file to the location you would like to pull the repository down to featuring:

```json
{
    "require": {
        "activecollab/activecollab-sdk": "~2.0"
    }
}
```
    
Run a ``composer update`` to install the package.

## First Connection

In order to connect, you will need API URL and API token. 

To get these details, go to your user profile in your activeCollab and select **API Subscriptions** from **Options** drop-down. Click on **New Subscription** button and fill the form (client name is name of your app that will communicate with activeCollab via API). After you create API subscription for your application, click on the magnifier glass icon to open a dialog that will show you API URL and token for that subscription.

Now that you have API token and URL, you can test out this simple example:

```php
<?php

  require_once 'vendor/autoload.php';

  use \ActiveCollab\Client as API;
  use \ActiveCollab\Exceptions\AppException;

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

# Making API Calls

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

For full list of available API command, please check [activeCollab API documentation](https://www.activecollab.com/docs/manuals/developers).