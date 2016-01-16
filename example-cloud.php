<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

require_once __DIR__ . '/vendor/autoload.php';

$authenticator = new \ActiveCollab\SDK\Authenticator\Cloud('ACME Inc', 'My Awesome Application', 'you@acmeinc.com', 'hard to guess, easy to remember');

// Show all Active Collab 5 and up account that this user has access to
print_r($authenticator->getAccounts());

// Show user details (first name, last name and avatar URL)
print_r($authenticator->getUser());

// Issue a token for account #123456789
$token = $authenticator->issueToken(123456789);

if ($token instanceof \ActiveCollab\SDK\TokenInterface) {
    print $token->getUrl() . "\n";
    print $token->getToken() . "\n";
} else {
    print "Invalid response\n";
    die();
}

// Create a client instance
$client = new \ActiveCollab\SDK\Client($token);

// Make a request
print_r($client->get('projects')->getJson());
