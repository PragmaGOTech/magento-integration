<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Http;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\Http\TransferInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\ApiClientInterface;
use Pragma\PragmaPayCore\Exception\ApiException;
use Pragma\PragmaPayCore\Gateway\Http\PragmaPayClient;

class PragmaPayClientTest extends TestCase
{
    private PragmaPayClient $pragmaPayClient;
    private MockObject $apiClient;
    private MockObject $json;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->apiClient = $this->createMock(ApiClientInterface::class);
        $this->json = $this->createMock(Json::class);

        // Arrange: Instantiate the PragmaPayClient
        $this->pragmaPayClient = new PragmaPayClient(
            $this->apiClient,
            $this->json
        );
    }

    public function testPlaceRequestReturnsUnserializedResponse(): void
    {
        // Arrange
        $transferObject = $this->createMock(TransferInterface::class);
        $uri = 'https://api.example.com';
        $body = ['key' => 'value'];
        $headers = ['Authorization' => 'Bearer token'];
        $method = 'POST';
        $responseRaw = '{"success":true}';
        $responseArray = ['success' => true];

        $transferObject->method('getUri')->willReturn($uri);
        $transferObject->method('getBody')->willReturn($body);
        $transferObject->method('getHeaders')->willReturn($headers);
        $transferObject->method('getMethod')->willReturn($method);

        $this->apiClient->method('submit')->with($uri, $body, $headers, $method)->willReturn($responseRaw);
        $this->json->method('unserialize')->with($responseRaw)->willReturn($responseArray);

        // Act
        $result = $this->pragmaPayClient->placeRequest($transferObject);

        // Assert
        $this->assertSame($responseArray, $result);
    }

    public function testPlaceRequestReturnsEmptyArrayOnApiException(): void
    {
        // Arrange
        $transferObject = $this->createMock(TransferInterface::class);
        $uri = 'https://api.example.com';
        $body = ['key' => 'value'];
        $headers = ['Authorization' => 'Bearer token'];
        $method = 'POST';

        $transferObject->method('getUri')->willReturn($uri);
        $transferObject->method('getBody')->willReturn($body);
        $transferObject->method('getHeaders')->willReturn($headers);
        $transferObject->method('getMethod')->willReturn($method);

        $this->apiClient->method('submit')->with($uri, $body, $headers, $method)->willThrowException(new ApiException());

        // Act
        $result = $this->pragmaPayClient->placeRequest($transferObject);

        // Assert
        $this->assertSame([], $result);
    }
}
