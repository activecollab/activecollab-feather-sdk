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

use ActiveCollab\SDK\Authenticator\Cloud;
use ActiveCollab\SDK\Client;
use ActiveCollab\SDK\Exceptions\TwoFactorAuthRequired;

$email = readline('Email: ');

echo 'Password: ';
shell_exec('stty -echo');
$password = rtrim(fgets(STDIN), "\n");
shell_exec('stty echo');
echo "\n";

$authenticator = new Cloud(
    'ACME Inc',
    'My Awesome Application',
    $email,
    $password,
);

try {
    $accounts = $authenticator->getAccounts();
} catch (TwoFactorAuthRequired $e) {
    $code = readline('2FA code: ');

    $authenticator->completeTwoFactorAuth($e->getIntentId(), $code);
    $accounts = $authenticator->getAccounts();
}

if (empty($accounts)) {
    die("No accounts found.\n");
}

echo "\nAvailable accounts:\n";

$indices = [];
$i = 1;

foreach ($accounts as $accountId => $account) {
    echo "  [{$i}] {$account['name']} (ID: {$accountId})\n";
    $indices[$i] = $accountId;
    $i++;
}

echo "\n";

$choice = (int) readline('Pick an account [number]: ');

if (!isset($indices[$choice])) {
    die("Invalid choice.\n");
}

$accountId = $indices[$choice];

echo "Issuing token for account {$accountId}...\n";

$token = $authenticator->issueToken($accountId);

echo "Authenticated successfully!\n";
echo "URL: " . $token->getUrl() . "\n";
echo "Token: " . $token->getToken() . "\n\n";

$client = new Client($token);

echo "Projects:\n";
print_r($client->get('projects')->getJson());
