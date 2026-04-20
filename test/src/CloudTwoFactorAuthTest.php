<?php

/*
 * This library is free software, and it is part of the Active Collab SDK project. Check LICENSE for details.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\SDK\Test;

use ActiveCollab\SDK\Authenticator\Cloud;
use ActiveCollab\SDK\ConnectorInterface;
use ActiveCollab\SDK\Exceptions\TwoFactorAuthRequired;
use ActiveCollab\SDK\ResponseInterface;
use PHPUnit\Framework\TestCase;

class CloudTwoFactorAuthTest extends TestCase
{
    private function createMockResponse(array $json): ResponseInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('isJson')->willReturn(true);
        $response->method('getJson')->willReturn($json);

        return $response;
    }

    private function createAuthenticator(ConnectorInterface $connector): Cloud
    {
        return new class ('ACME Inc', 'Test App', 'test@example.com', 'password123', $connector) extends Cloud {
            private ConnectorInterface $testConnector;

            public function __construct(
                string $org,
                string $app,
                string $email,
                string $password,
                ConnectorInterface $connector
            ) {
                parent::__construct($org, $app, $email, $password);
                $this->testConnector = $connector;
            }

            protected function getConnector()
            {
                return $this->testConnector;
            }
        };
    }

    private function getSuccessResponse(): array
    {
        return [
            'is_ok' => 1,
            'accounts' => [
                [
                    'name' => '123456',
                    'display_name' => 'Test Account',
                    'url' => 'https://app.activecollab.com/123456',
                    'class' => 'FeatherApplicationInstance',
                    'intent' => 'jwt-token-123',
                ],
            ],
            'user' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
            ],
        ];
    }

    public function testTwoFactorChallengeIsDetected(): void
    {
        $connector = $this->createMock(ConnectorInterface::class);
        $connector
            ->method('post')
            ->willReturn($this->createMockResponse(['intent_id' => 'test-intent-id']));

        $authenticator = $this->createAuthenticator($connector);

        try {
            $authenticator->getAccounts();
            $this->fail('Expected TwoFactorAuthRequired exception was not thrown');
        } catch (TwoFactorAuthRequired $e) {
            $this->assertSame('test-intent-id', $e->getIntentId());
            $this->assertSame('Two-factor authentication is required', $e->getMessage());
        }
    }

    public function testTwoFactorVerificationCompletesLogin(): void
    {
        $twoFactorResponse = $this->createMockResponse(['intent_id' => 'test-intent-id']);
        $successResponse = $this->createMockResponse($this->getSuccessResponse());

        $connector = $this->createMock(ConnectorInterface::class);
        $connector
            ->method('post')
            ->willReturnOnConsecutiveCalls($twoFactorResponse, $successResponse);

        $authenticator = $this->createAuthenticator($connector);

        // First call triggers 2FA
        try {
            $authenticator->getAccounts();
            $this->fail('Expected TwoFactorAuthRequired exception');
        } catch (TwoFactorAuthRequired $e) {
            $this->assertSame('test-intent-id', $e->getIntentId());
        }

        // Complete 2FA
        $authenticator->completeTwoFactorAuth('test-intent-id', '123456');

        // Now accounts, user, and intent should be available
        $accounts = $authenticator->getAccounts();
        $this->assertArrayHasKey(123456, $accounts);
        $this->assertSame('Test Account', $accounts[123456]['name']);

        $user = $authenticator->getUser();
        $this->assertSame('John', $user['first_name']);
        $this->assertSame('Doe', $user['last_name']);
    }

    public function testIssueTokenWorksAfterTwoFactorAuth(): void
    {
        $twoFactorResponse = $this->createMockResponse(['intent_id' => 'test-intent-id']);
        $successResponse = $this->createMockResponse($this->getSuccessResponse());
        $tokenResponse = $this->createMockResponse([
            'is_ok' => 1,
            'token' => 'issued-token-abc',
        ]);

        $connector = $this->createMock(ConnectorInterface::class);
        $connector
            ->method('post')
            ->willReturnOnConsecutiveCalls($twoFactorResponse, $successResponse, $tokenResponse);

        $authenticator = $this->createAuthenticator($connector);

        // Trigger 2FA
        try {
            $authenticator->getAccounts();
            $this->fail('Expected TwoFactorAuthRequired exception');
        } catch (TwoFactorAuthRequired $e) {
            // expected
        }

        // Complete 2FA
        $authenticator->completeTwoFactorAuth($e->getIntentId(), '123456');

        // Issue token for the loaded account
        $token = $authenticator->issueToken(123456);
        $this->assertSame('issued-token-abc', $token->getToken());
        $this->assertSame('https://app.activecollab.com/123456', $token->getUrl());
    }
}
