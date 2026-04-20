<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

use ActiveCollab\SDK\Authenticator\SelfHosted;
use ActiveCollab\SDK\Client;
use ActiveCollab\SDK\TokenInterface;

$email = readline('Email: ');
$url = readline('Self-hosted URL: ');

echo 'Password: ';
shell_exec('stty -echo');
$password = rtrim(fgets(STDIN), "\n");
shell_exec('stty echo');
echo "\n";

$authenticator = new SelfHosted(
    'ACME Inc',
    'My Awesome Application',
    $email,
    $password,
    $url,
);

$token = $authenticator->issueToken();

if ($token instanceof TokenInterface) {
    echo "Authenticated successfully!\n";
    echo "URL: " . $token->getUrl() . "\n";
    echo "Token: " . $token->getToken() . "\n\n";
} else {
    die("Authentication failed.\n");
}

$client = new Client($token);

echo "Projects:\n";
print_r($client->get('projects')->getJson());
