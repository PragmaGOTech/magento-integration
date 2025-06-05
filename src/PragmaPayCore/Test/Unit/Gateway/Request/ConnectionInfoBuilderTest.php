<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Request;

use InvalidArgumentException;
use Magento\Framework\Url;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Gateway\Request\ConnectionInfoBuilder;

class ConnectionInfoBuilderTest extends TestCase
{
    private ConnectionInfoBuilder $connectionInfoBuilder;
    private PragmaConnectionConfigProviderInterface $configProvider;
    private Url $urlBuilder;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->configProvider = $this->createMock(PragmaConnectionConfigProviderInterface::class);
        $this->urlBuilder = $this->createMock(Url::class);

        // Instantiate the ConnectionInfoBuilder with mocked dependencies
        $this->connectionInfoBuilder = new ConnectionInfoBuilder($this->configProvider, $this->urlBuilder);
    }

    public function testBuildWithValidPayment(): void
    {
        // Arrange
        $storeId = 1;
        $notificationUrl = 'https://example.com/notification';
        $returnUrl = 'https://example.com/return';
        $cancelUrl = 'https://example.com/cancel';

        $orderAdapter = $this->createMock(OrderAdapterInterface::class);
        $orderAdapter->method('getStoreId')->willReturn($storeId);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getOrder')->willReturn($orderAdapter);

        $this->configProvider->method('getNotificationUrl')->with($storeId)->willReturn('notification');
        $this->configProvider->method('getReturnUrl')->with($storeId)->willReturn('return');
        $this->configProvider->method('getCancelUrl')->with($storeId)->willReturn('cancel');
        $this->urlBuilder->method('getUrl')
            ->willReturnCallback(function (string $route, $routeParams) use ($notificationUrl, $returnUrl, $cancelUrl) {
            if (isset($routeParams['_direct'])) {
                return $notificationUrl;
            }
            return match ($route) {
                'return' => $returnUrl,
                'cancel' => $cancelUrl,
                default => null,
            };
        });

        $buildSubject = ['payment' => $paymentDataObject];

        // Act
        $result = $this->connectionInfoBuilder->build($buildSubject);

        // Assert
        $this->assertArrayHasKey('body', $result);
        $this->assertSame([
            'notificationUrl' => $notificationUrl,
            'returnUrl' => $returnUrl,
            'cancelUrl' => $cancelUrl,
        ], $result['body']);
    }

    public function testBuildWithInvalidPaymentThrowsException(): void
    {
        // Arrange
        $buildSubject = ['payment' => null];

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment data object should be provided');

        // Act
        $this->connectionInfoBuilder->build($buildSubject);
    }
}
