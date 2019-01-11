# PHP SDK for ActiveCollab 5 and 6 API

This is a simple PHP library that makes communication with [Active Collab API](https://developers.activecollab.com/api-documentation/) easy.

## Installation

If you choose to install this application with Composer instead of pulling down the git repository you will need to add a composer.json file to the location you would like to pull the repository down to featuring:

```json
{
    "require": {
        "activecollab/activecollab-feather-sdk": "^3.0"
    }
}
```
    
Run a `composer update` to install the package.

*Note*: If you used an older version of Active Collab API wrapper and loaded it using `dev-master`, lock it to version 2.0 by setting require statement to `^2.0` and calling `composer update`.

## Connecting to Active Collab Cloud Accounts

```php
<?php

require_once '/path/to/vendor/autoload.php';

// Provide name of your company, name of the app that you are developing, your email address and password.
$authenticator = new \ActiveCollab\SDK\Authenticator\Cloud('ACME Inc', 'My Awesome Application', 'you@acmeinc.com', 'hard to guess, easy to remember');

// Show all Active Collab 5 and up account that this user has access to.
print_r($authenticator->getAccounts());

// Show user details (first name, last name and avatar URL).
print_r($authenticator->getUser());

// Issue a token for account #123456789.
$token = $authenticator->issueToken(123456789);

// Did we get it?
if ($token instanceof \ActiveCollab\SDK\TokenInterface) {
    print $token->getUrl() . "\n";
    print $token->getToken() . "\n";
} else {
    print "Invalid response\n";
    die();
}
```

## Connecting to Self-Hosted Active Collab Accounts

```php
require_once '/path/to/vendor/autoload.php';

// Provide name of your company, name of the app that you are developing, your email address and password. Last parameter is URL where your Active Collab is installed.
$authenticator = new \ActiveCollab\SDK\Authenticator\SelfHosted('ACME Inc', 'My Awesome Application', 'you@acmeinc.com', 'hard to guess, easy to remember', 'https://my.company.com/projects');

// Issue a token.
$token = $authenticator->issueToken();

// Did we get what we asked for?
if ($token instanceof \ActiveCollab\SDK\TokenInterface) {
    print $token->getUrl() . "\n";
    print $token->getToken() . "\n";
} else {
    print "Invalid response\n";
    die();
}
```

## SSL problems?

If curl complains that SSL peer verification has failed, you can turn it off like this:

```php
// Cloud
$authenticator = new \ActiveCollab\SDK\Authenticator\Cloud('ACME Inc', 'My Awesome Application', 'you@acmeinc.com', 'hard to guess, easy to remember', false);
$authenticator->setSslVerifyPeer(false);

// Self-hosted
$authenticator = new \ActiveCollab\SDK\Authenticator\SelfHosted('ACME Inc', 'My Awesome Application', 'you@acmeinc.com', 'hard to guess, easy to remember', 'https://my.company.com/projects', false);
$authenticator->setSslVerifyPeer(false);

// Client
$client = new \ActiveCollab\SDK\Client($token);
$client->setSslVerifyPeer(false);
```

**Note:** Option to turn off SSL peer verification has been added in Active Collab SDK 3.1.

## Constructing a client instance

Once we have our token, we can construct a client and make API calls:

```php
$client = new \ActiveCollab\SDK\Client($token);
```

Listing all tasks in project #65 is easy. Just call:

```php
$client->get('projects/65/tasks');
```

To create a task, simply send a POST request:

```php
try {
    $client->post('projects/65/tasks', [
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
    $client->put('projects/65/tasks/123', [
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
    $client->delete('projects/65/tasks/123');
} catch(AppException $e) {
    print $e->getMessage() . '<br><br>';
    // var_dump($e->getServerResponse()); (need more info?)
}
```

``delete()`` method only requires ``command`` argument to be provided.

For full list of available API command, please check [Active Collab API documentation](https://developers.activecollab.com/api-documentation/).
