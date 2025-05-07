<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Request;

use InvalidArgumentException;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Gateway\Request\CreatePaymentUriBuilder;

class CreatePaymentUriBuilderTest extends TestCase
{
    private CreatePaymentUriBuilder $createPaymentUriBuilder;
    private PragmaConnectionConfigProviderInterface $configProvider;

    protected function setUp(): void
    {
        // Arrange: Mock the PragmaConnectionConfigProviderInterface
        $this->configProvider = $this->createMock(PragmaConnectionConfigProviderInterface::class);

        // Instantiate the CreatePaymentUriBuilder with the mocked dependency
        $this->createPaymentUriBuilder = new CreatePaymentUriBuilder($this->configProvider);
    }

    public function testBuildWithSandboxMode(): void
    {
        // Arrange
        $storeId = 1;

        $orderAdapter = $this->createMock(OrderAdapterInterface::class);
        $orderAdapter->method('getStoreId')->willReturn($storeId);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getOrder')->willReturn($orderAdapter);

        $this->configProvider->method('isSandbox')->with($storeId)->willReturn(true);

        $buildSubject = ['payment' => $paymentDataObject];

        // Act
        $result = $this->createPaymentUriBuilder->build($buildSubject);

        // Assert
        $this->assertArrayHasKey('uri', $result);
        $this->assertSame('api/v2/partner/payment', $result['uri']);
    }

    public function testBuildWithProductionMode(): void
    {
        // Arrange
        $storeId = 1;

        $orderAdapter = $this->createMock(OrderAdapterInterface::class);
        $orderAdapter->method('getStoreId')->willReturn($storeId);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getOrder')->willReturn($orderAdapter);

        $this->configProvider->method('isSandbox')->with($storeId)->willReturn(false);

        $buildSubject = ['payment' => $paymentDataObject];

        // Act
        $result = $this->createPaymentUriBuilder->build($buildSubject);

        // Assert
        $this->assertArrayHasKey('uri', $result);
        $this->assertSame('api/v2/partner/payment', $result['uri']);
    }

    public function testBuildWithInvalidPaymentThrowsException(): void
    {
        // Arrange
        $buildSubject = ['payment' => null];

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment data object should be provided');

        // Act
        $this->createPaymentUriBuilder->build($buildSubject);
    }
}
