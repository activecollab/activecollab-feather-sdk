<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

require_once __DIR__ . '/vendor/autoload.php';

// Construct a self-hosted authenticator. Last parameter is URL where your Active Collab
$authenticator = new \ActiveCollab\SDK\Authenticator\SelfHosted('ACME Inc', 'My Awesome Application', 'you@acmeinc.com', 'hard to guess, easy to remember', 'https://my.company.com/projects');

// Issue a token
$token = $authenticator->issueToken();

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
