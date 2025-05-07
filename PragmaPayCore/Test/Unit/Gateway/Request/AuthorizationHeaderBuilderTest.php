<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Request;

use InvalidArgumentException;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\AuthorizationTokenProviderInterface;
use Pragma\PragmaPayCore\Gateway\Request\AuthorizationHeaderBuilder;

class AuthorizationHeaderBuilderTest extends TestCase
{
    private AuthorizationHeaderBuilder $authorizationHeaderBuilder;
    private AuthorizationTokenProviderInterface $authorizationTokenProvider;

    protected function setUp(): void
    {
        // Arrange: Mock the AuthorizationTokenProviderInterface
        $this->authorizationTokenProvider = $this->createMock(AuthorizationTokenProviderInterface::class);

        // Instantiate the AuthorizationHeaderBuilder with the mocked dependency
        $this->authorizationHeaderBuilder = new AuthorizationHeaderBuilder($this->authorizationTokenProvider);
    }

    public function testBuildWithValidPayment(): void
    {
        // Arrange
        $storeId = 1;
        $accessToken = 'test_access_token';

        $orderAdapter = $this->createMock(OrderAdapterInterface::class);
        $orderAdapter->method('getStoreId')->willReturn($storeId);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getOrder')->willReturn($orderAdapter);

        $this->authorizationTokenProvider->method('getAccessToken')->with($storeId)->willReturn($accessToken);

        $buildSubject = ['payment' => $paymentDataObject];

        // Act
        $result = $this->authorizationHeaderBuilder->build($buildSubject);

        // Assert
        $this->assertArrayHasKey('headers', $result);
        $this->assertArrayHasKey('authorization', $result['headers']);
        $this->assertSame('Bearer ' . $accessToken, $result['headers']['authorization']);
    }

    public function testBuildWithInvalidPaymentThrowsException(): void
    {
        // Arrange
        $buildSubject = ['payment' => null];

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment data object should be provided');

        // Act
        $this->authorizationHeaderBuilder->build($buildSubject);
    }
}
