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
use Pragma\PragmaPayCore\Gateway\Request\PragmaPaymentIdBuilder;

class PragmaPaymentIdBuilderTest extends TestCase
{
    private PragmaPaymentIdBuilder $pragmaPaymentIdBuilder;

    protected function setUp(): void
    {
        // Arrange: Instantiate the PragmaPaymentIdBuilder
        $this->pragmaPaymentIdBuilder = new PragmaPaymentIdBuilder();
    }

    public function testBuildWithValidPayment(): void
    {
        // Arrange
        $pragmaPaymentId = '12345';

        $orderPayment = $this->createMock(OrderPaymentInterface::class);
        $orderPayment->method('getAdditionalInformation')
            ->with(PragmaConnectionConfigProviderInterface::ADDITION_KEY_PAYMENT)
            ->willReturn($pragmaPaymentId);

        $orderAdapter = $this->createMock(OrderAdapterInterface::class);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getPayment')->willReturn($orderPayment);
        $paymentDataObject->method('getOrder')->willReturn($orderAdapter);

        $buildSubject = ['payment' => $paymentDataObject];

        // Act
        $result = $this->pragmaPaymentIdBuilder->build($buildSubject);

        // Assert
        $this->assertArrayHasKey('body', $result);
        $this->assertSame(['paymentId' => $pragmaPaymentId], $result['body']);
    }

    public function testBuildWithInvalidPaymentThrowsException(): void
    {
        // Arrange
        $buildSubject = ['payment' => null];

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment data object should be provided');

        // Act
        $this->pragmaPaymentIdBuilder->build($buildSubject);
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
        $this->pragmaPaymentIdBuilder->build($buildSubject);
    }

    public function testBuildWithMissingPragmaPaymentIdThrowsException(): void
    {
        // Arrange
        $orderPayment = $this->createMock(OrderPaymentInterface::class);
        $orderPayment->method('getAdditionalInformation')
            ->with(PragmaConnectionConfigProviderInterface::ADDITION_KEY_PAYMENT)
            ->willReturn(null);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getPayment')->willReturn($orderPayment);

        $buildSubject = ['payment' => $paymentDataObject];

        // Assert
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Pragma payment id should be provided.');

        // Act
        $this->pragmaPaymentIdBuilder->build($buildSubject);
    }
}
