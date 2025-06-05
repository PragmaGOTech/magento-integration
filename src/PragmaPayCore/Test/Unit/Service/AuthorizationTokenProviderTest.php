<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\ApiClientInterface;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Exception\ApiException;
use Pragma\PragmaPayCore\Service\AuthorizationTokenProvider;
use Psr\Log\LoggerInterface;

class AuthorizationTokenProviderTest extends TestCase
{
    private AuthorizationTokenProvider $authorizationTokenProvider;
    private ApiClientInterface $apiClient;
    private PragmaConnectionConfigProviderInterface $connectionConfigProvider;
    private Json $json;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->apiClient = $this->createMock(ApiClientInterface::class);
        $this->connectionConfigProvider = $this->createMock(PragmaConnectionConfigProviderInterface::class);
        $this->json = $this->createMock(Json::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        // Arrange: Instantiate the AuthorizationTokenProvider
        $this->authorizationTokenProvider = new AuthorizationTokenProvider(
            $this->apiClient,
            $this->connectionConfigProvider,
            $this->json,
            $this->logger
        );
    }

    public function testGetAccessTokenReturnsToken(): void
    {
        // Arrange
        $storeId = 1;
        $partnerKey = 'partner_key';
        $partnerSecret = 'partner_secret';
        $response = '{"token":"new_token"}';
        $newToken = 'new_token';

        $this->connectionConfigProvider->method('getPartnerKey')->with($storeId)->willReturn($partnerKey);
        $this->connectionConfigProvider->method('getPartnerSecret')->with($storeId)->willReturn($partnerSecret);
        $this->connectionConfigProvider->method('isSandbox')->with($storeId)->willReturn(false);
        $this->apiClient->method('submit')->willReturn($response);
        $this->json->method('unserialize')->with($response)->willReturn(['token' => $newToken]);

        // Act
        $result = $this->authorizationTokenProvider->getAccessToken($storeId);

        // Assert
        $this->assertSame($newToken, $result);
    }

    public function testGetAccessTokenThrowsExceptionOnMissingToken(): void
    {
        // Arrange
        $storeId = 1;
        $response = '{}';

        $this->connectionConfigProvider->method('getPartnerKey')->with($storeId)->willReturn('partner_key');
        $this->connectionConfigProvider->method('getPartnerSecret')->with($storeId)->willReturn('partner_secret');
        $this->connectionConfigProvider->method('isSandbox')->with($storeId)->willReturn(false);
        $this->apiClient->method('submit')->willReturn($response);
        $this->json->method('unserialize')->with($response)->willReturn([]);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Token not found in response.');

        // Act
        $this->authorizationTokenProvider->getAccessToken($storeId);
    }

    public function testGetAccessTokenHandlesApiException(): void
    {
        // Arrange
        $storeId = 1;

        $this->connectionConfigProvider->method('getPartnerKey')->with($storeId)->willReturn('partner_key');
        $this->connectionConfigProvider->method('getPartnerSecret')->with($storeId)->willReturn('partner_secret');
        $this->connectionConfigProvider->method('isSandbox')->with($storeId)->willReturn(false);
        $this->apiClient->method('submit')->willThrowException(new ApiException('API error'));

        $this->logger->expects($this->once())->method('error')->with(
            'HTTP request to API failed.',
            $this->callback(fn($context) => isset($context['exception']))
        );

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Communication error with API.');

        // Act
        $this->authorizationTokenProvider->getAccessToken($storeId);
    }
}
