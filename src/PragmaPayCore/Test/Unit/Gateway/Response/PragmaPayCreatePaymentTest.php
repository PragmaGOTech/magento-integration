<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Gateway\Response\PragmaPayCreatePayment;

class PragmaPayCreatePaymentTest extends TestCase
{
    public function testHandleSetsTransactionAndAdditionalInformation(): void
    {
        // Arrange
        $paymentMock = $this->createMock(Payment::class);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getPayment')->willReturn($paymentMock);

        $handlingSubject = ['payment' => $paymentDataObject];

        $response = [
            PragmaConnectionConfigProviderInterface::API_RESPONSE_PAYMENT_ID_FIELD => 'payment-id-123',
            PragmaConnectionConfigProviderInterface::API_RESPONSE_REDIRECT_URL_FIELD => 'https://redirect.url',
            PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEMS_FIELD => [
                [
                    PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEM_ID_FIELD => 'item-456'
                ]
            ]
        ];

        $paymentMock->expects($this->once())->method('setTransactionId')->with('payment-id-123')->willReturnSelf();
        $paymentMock->expects($this->once())->method('setIsTransactionPending')->with(true)->willReturnSelf();
        $paymentMock->expects($this->once())->method('setIsTransactionClosed')->with(false)->willReturnSelf();

        $paymentMock->expects($this->exactly(3))->method('setAdditionalInformation')
            ->willReturnCallback(function ($key, $value) {
                $this->assertContains($key, [
                    PragmaConnectionConfigProviderInterface::ADDITION_KEY_REDIRECT_URL,
                    PragmaConnectionConfigProviderInterface::ADDITION_KEY_PAYMENT,
                    PragmaConnectionConfigProviderInterface::ADDITION_KEY_ITEM_ID
                ]);
                return $this;
            });

        $handler = new PragmaPayCreatePayment();

        // Act
        $handler->handle($handlingSubject, $response);

        // Assert
        $this->assertTrue(true);
    }

    public function testHandleWithoutItemId(): void
    {
        // Arrange
        $paymentMock = $this->createMock(Payment::class);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getPayment')->willReturn($paymentMock);

        $handlingSubject = ['payment' => $paymentDataObject];

        $response = [
            PragmaConnectionConfigProviderInterface::API_RESPONSE_PAYMENT_ID_FIELD => 'payment-id-123',
            PragmaConnectionConfigProviderInterface::API_RESPONSE_REDIRECT_URL_FIELD => 'https://redirect.url',
            PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEMS_FIELD => []
        ];

        $paymentMock->expects($this->once())->method('setTransactionId')->with('payment-id-123')->willReturnSelf();
        $paymentMock->expects($this->once())->method('setIsTransactionPending')->with(true)->willReturnSelf();
        $paymentMock->expects($this->once())->method('setIsTransactionClosed')->with(false)->willReturnSelf();

        $paymentMock->expects($this->exactly(2))->method('setAdditionalInformation')
            ->willReturnCallback(function ($key, $value) {
                $this->assertContains($key, [
                    PragmaConnectionConfigProviderInterface::ADDITION_KEY_REDIRECT_URL,
                    PragmaConnectionConfigProviderInterface::ADDITION_KEY_PAYMENT
                ]);
                return $this;
            });

        $handler = new PragmaPayCreatePayment();

        // Act
        $handler->handle($handlingSubject, $response);

        // Assert
        $this->assertTrue(true);
    }

    public function testHandleWithMissingFields(): void
    {
        // Arrange
        $paymentMock = $this->createMock(Payment::class);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getPayment')->willReturn($paymentMock);

        $handlingSubject = ['payment' => $paymentDataObject];

        $response = [
            PragmaConnectionConfigProviderInterface::API_RESPONSE_PAYMENT_ID_FIELD => null,
            PragmaConnectionConfigProviderInterface::API_RESPONSE_REDIRECT_URL_FIELD => null,
            PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEMS_FIELD => []
        ];

        $paymentMock->expects($this->once())->method('setTransactionId')->with(null)->willReturnSelf();
        $paymentMock->expects($this->once())->method('setIsTransactionPending')->with(true)->willReturnSelf();
        $paymentMock->expects($this->once())->method('setIsTransactionClosed')->with(false)->willReturnSelf();

        $paymentMock->expects($this->exactly(2))->method('setAdditionalInformation')
            ->willReturnCallback(function ($key, $value) {
                $this->assertContains($key, [
                    PragmaConnectionConfigProviderInterface::ADDITION_KEY_REDIRECT_URL,
                    PragmaConnectionConfigProviderInterface::ADDITION_KEY_PAYMENT
                ]);
                return $this;
            });

        $handler = new PragmaPayCreatePayment();

        // Act
        $handler->handle($handlingSubject, $response);

        // Assert
        $this->assertTrue(true);
    }
}
