<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Request;

use InvalidArgumentException;
use LogicException;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Gateway\Request\UriWithPaymentIdPathParamBuilder;

class UriWithPaymentIdPathParamBuilderTest extends TestCase
{
    private UriWithPaymentIdPathParamBuilder $uriBuilder;
    private PragmaConnectionConfigProviderInterface $configProvider;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->configProvider = $this->createMock(PragmaConnectionConfigProviderInterface::class);

        // Instantiate the UriWithPaymentIdPathParamBuilder with mocked dependencies
        $this->uriBuilder = new UriWithPaymentIdPathParamBuilder(
            $this->configProvider,
            UriWithPaymentIdPathParamBuilder::CANCEL_PAYMENT_URI,
            UriWithPaymentIdPathParamBuilder::CANCEL_SANDBOX_PAYMENT_URI
        );
    }

    public function testBuildWithValidData(): void
    {
        // Arrange
        $storeId = 1;
        $pragmaPaymentId = '12345';
        $expectedUri = sprintf(UriWithPaymentIdPathParamBuilder::CANCEL_PAYMENT_URI, $pragmaPaymentId);

        $orderPayment = $this->createMock(OrderPaymentInterface::class);
        $orderPayment->method('getAdditionalInformation')
            ->with(PragmaConnectionConfigProviderInterface::ADDITION_KEY_PAYMENT)
            ->willReturn($pragmaPaymentId);

        $orderAdapter = $this->createMock(OrderAdapterInterface::class);
        $orderAdapter->method('getStoreId')->willReturn($storeId);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getPayment')->willReturn($orderPayment);
        $paymentDataObject->method('getOrder')->willReturn($orderAdapter);

        $this->configProvider->method('isSandbox')->with($storeId)->willReturn(false);

        $buildSubject = ['payment' => $paymentDataObject];

        // Act
        $result = $this->uriBuilder->build($buildSubject);

        // Assert
        $this->assertArrayHasKey('uri', $result);
        $this->assertSame($expectedUri, $result['uri']);
    }

    public function testBuildWithInvalidPaymentThrowsException(): void
    {
        // Arrange
        $buildSubject = ['payment' => null];

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment data object should be provided');

        // Act
        $this->uriBuilder->build($buildSubject);
    }

    public function testBuildWithInvalidOrderPaymentThrowsException(): void
    {
        // Arrange
        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getPayment')->willReturn(null);

        $buildSubject = ['payment' => $paymentDataObject];

        // Assert
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Order payment should be provided.');

        // Act
        $this->uriBuilder->build($buildSubject);
    }

    public function testBuildWithMissingPragmaPaymentIdThrowsException(): void
    {
        // Arrange
        $orderPayment = $this->createMock(OrderPaymentInterface::class);
        $orderPayment->method('getAdditionalInformation')
            ->with(PragmaConnectionConfigProviderInterface::ADDITION_KEY_PAYMENT)
            ->willReturn(null);

        $orderAdapter = $this->createMock(OrderAdapterInterface::class);
        $orderAdapter->method('getStoreId')->willReturn(1);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getPayment')->willReturn($orderPayment);
        $paymentDataObject->method('getOrder')->willReturn($orderAdapter);

        $buildSubject = ['payment' => $paymentDataObject];

        // Assert
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Pragma payment id should be provided.');

        // Act
        $this->uriBuilder->build($buildSubject);
    }
}
