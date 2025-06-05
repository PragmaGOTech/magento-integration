<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Client;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Client\ApiClient;
use Pragma\PragmaPayCore\Exception\ApiException;
use Psr\Log\LoggerInterface;

class ApiClientTest extends TestCase
{
    private ApiClient $apiClient;
    private PragmaConnectionConfigProviderInterface $connectionConfigProvider;
    private Json $json;
    private StoreManagerInterface $storeManager;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->connectionConfigProvider = $this->createMock(PragmaConnectionConfigProviderInterface::class);
        $this->json = new Json();
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->apiClient = new ApiClient(
            $this->connectionConfigProvider,
            $this->json,
            $this->createMock(\GuzzleHttp\ClientFactory::class),
            $this->storeManager,
            $this->logger
        );
    }

    public function testSubmitHandlesException(): void
    {
        // Arrange
        $actionUri = '/test-uri';
        $params = ['key' => 'value'];
        $headers = [];
        $method = 'POST';

        $storeId = 1;

        // Mock the store object
        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn($storeId);

        // Mock the store manager to return the store object
        $this->storeManager->method('getStore')->willReturn($store);

        $request = new Request('POST', $actionUri);
        $response = new Response(400, [], 'Bad Request');
        $exception = new BadResponseException('Error', $request, $response);

        $client = $this->createMock(\GuzzleHttp\Client::class);
        $client->method('request')->willThrowException($exception);

        $clientFactory = $this->createMock(\GuzzleHttp\ClientFactory::class);
        $clientFactory->method('create')->willReturn($client);

        $this->apiClient = new ApiClient(
            $this->connectionConfigProvider,
            $this->json,
            $clientFactory,
            $this->storeManager,
            $this->logger
        );

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Something went wrong with your request. Please try again later.');

        // Act
        $this->apiClient->submit($actionUri, $params, $headers, $method);
    }

    public function testSubmitWithEmptyParams(): void
    {
        // Arrange
        $actionUri = '/test-uri';
        $params = [];
        $headers = [];
        $method = 'POST';

        $storeId = 1;
        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn($storeId);
        $this->storeManager->method('getStore')->willReturn($store);

        $response = new Response(200, [], '{"success": true}');
        $client = $this->createMock(\GuzzleHttp\Client::class);
        $client->method('request')->willReturn($response);

        $clientFactory = $this->createMock(\GuzzleHttp\ClientFactory::class);
        $clientFactory->method('create')->willReturn($client);

        $this->apiClient = new ApiClient(
            $this->connectionConfigProvider,
            $this->json,
            $clientFactory,
            $this->storeManager,
            $this->logger
        );

        // Act
        $result = $this->json->unserialize($this->apiClient->submit($actionUri, $params, $headers, $method));

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    public function testSubmitWithInvalidHttpMethod(): void
    {
        // Arrange
        $actionUri = '/test-uri';
        $params = ['key' => 'value'];
        $headers = [];
        $method = 'INVALID';

        $storeId = 1;
        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn($storeId);
        $this->storeManager->method('getStore')->willReturn($store);

        $client = $this->createMock(\GuzzleHttp\Client::class);
        $clientFactory = $this->createMock(\GuzzleHttp\ClientFactory::class);
        $clientFactory->method('create')->willReturn($client);

        $this->apiClient = new ApiClient(
            $this->connectionConfigProvider,
            $this->json,
            $clientFactory,
            $this->storeManager,
            $this->logger
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP method');

        // Act
        $this->apiClient->submit($actionUri, $params, $headers, $method);
    }

    public function testSubmitHandlesTimeoutException(): void
    {
        // Arrange
        $actionUri = '/test-uri';
        $params = ['key' => 'value'];
        $headers = [];
        $method = 'POST';

        $storeId = 1;
        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn($storeId);
        $this->storeManager->method('getStore')->willReturn($store);

        $exception = new \GuzzleHttp\Exception\RequestException(
            'Timeout',
            new Request('POST', $actionUri)
        );

        $client = $this->createMock(\GuzzleHttp\Client::class);
        $client->method('request')->willThrowException($exception);

        $clientFactory = $this->createMock(\GuzzleHttp\ClientFactory::class);
        $clientFactory->method('create')->willReturn($client);

        $this->apiClient = new ApiClient(
            $this->connectionConfigProvider,
            $this->json,
            $clientFactory,
            $this->storeManager,
            $this->logger
        );

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Request timed out');

        // Act
        $this->apiClient->submit($actionUri, $params, $headers, $method);
    }

    public function testSubmitHandlesNonJsonResponse(): void
    {
        // Arrange
        $actionUri = '/test-uri';
        $params = ['key' => 'value'];
        $headers = [];
        $method = 'POST';

        $storeId = 1;
        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn($storeId);
        $this->storeManager->method('getStore')->willReturn($store);

        $response = new Response(200, [], 'Non-JSON Response');
        $client = $this->createMock(\GuzzleHttp\Client::class);
        $client->method('request')->willReturn($response);

        $clientFactory = $this->createMock(\GuzzleHttp\ClientFactory::class);
        $clientFactory->method('create')->willReturn($client);

        $this->apiClient = new ApiClient(
            $this->connectionConfigProvider,
            $this->json,
            $clientFactory,
            $this->storeManager,
            $this->logger
        );

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid response format');

        // Act
        $this->apiClient->submit($actionUri, $params, $headers, $method);
    }

    public function testSubmitWithMissingHeaders(): void
    {
        // Arrange
        $actionUri = '/test-uri';
        $params = ['key' => 'value'];
        $headers = []; // No headers provided
        $method = 'POST';

        $storeId = 1;
        $store = $this->createMock(StoreInterface::class);
        $store->method('getId')->willReturn($storeId);
        $this->storeManager->method('getStore')->willReturn($store);

        $response = new Response(200, [], '{"success": true}');
        $client = $this->createMock(\GuzzleHttp\Client::class);
        $client->method('request')->willReturn($response);

        $clientFactory = $this->createMock(\GuzzleHttp\ClientFactory::class);
        $clientFactory->method('create')->willReturn($client);

        $this->apiClient = new ApiClient(
            $this->connectionConfigProvider,
            $this->json,
            $clientFactory,
            $this->storeManager,
            $this->logger
        );

        // Act
        $result = $this->json->unserialize($this->apiClient->submit($actionUri, $params, $headers, $method));

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }
}
