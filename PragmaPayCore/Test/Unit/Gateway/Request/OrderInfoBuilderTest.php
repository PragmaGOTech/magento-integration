<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Request;

use InvalidArgumentException;
use LogicException;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Client\RequestManager;
use Pragma\PragmaPayCore\Gateway\Request\OrderInfoBuilder;
use Pragma\PragmaPayCore\Service\GenerateUuid5;

class OrderInfoBuilderTest extends TestCase
{
    private OrderInfoBuilder $orderInfoBuilder;
    private RequestManager $requestManager;
    private GenerateUuid5 $generateUuid5;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->requestManager = $this->createMock(RequestManager::class);
        $this->generateUuid5 = new GenerateUuid5();

        // Instantiate the OrderInfoBuilder with mocked dependencies
        $this->orderInfoBuilder = new OrderInfoBuilder(
            $this->requestManager,
            $this->generateUuid5
        );
    }

    public function testBuildWithValidData(): void
    {
        // Arrange
        $storeId = 1;
        $customerId = '123';
        $orderIncrementId = '000001';
        $currencyCode = 'USD';
        $formattedAmount = 10000;
        $grandTotal = 100.00;

        $orderAdapter = $this->createMock(OrderAdapterInterface::class);
        $orderAdapter->method('getStoreId')->willReturn($storeId);
        $orderAdapter->method('getCustomerId')->willReturn($customerId);
        $orderAdapter->method('getOrderIncrementId')->willReturn($orderIncrementId);
        $orderAdapter->method('getCurrencyCode')->willReturn($currencyCode);
        $orderAdapter->method('getGrandTotalAmount')->willReturn($grandTotal);

        $orderPayment = $this->createMock(OrderPaymentInterface::class);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getOrder')->willReturn($orderAdapter);
        $paymentDataObject->method('getPayment')->willReturn($orderPayment);

        $this->requestManager->method('formatAmount')->with($grandTotal)->willReturn($formattedAmount);

        $buildSubject = ['payment' => $paymentDataObject];

        // Act
        $result = $this->orderInfoBuilder->build($buildSubject);

        // Assert
        $this->assertArrayHasKey('body', $result);
        $this->assertEqualsCanonicalizing([
            'partnerCustomerId' => $this->generateUuid5->execute((string)$customerId),
            'partnerOrderId' => $this->generateUuid5->execute($orderIncrementId),
            'items' => [
                array_merge(
                    [
                        'partnerItemId' => $this->generateUuid5->execute($orderIncrementId),
                        'value' => [
                            'amount' => $formattedAmount,
                            'currency' => $currencyCode,
                        ],
                    ],
                )
            ]
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
        $this->orderInfoBuilder->build($buildSubject);
    }

    public function testBuildWithInvalidOrderPaymentThrowsException(): void
    {
        // Arrange
        $orderAdapter = $this->createMock(OrderAdapterInterface::class);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getOrder')->willReturn($orderAdapter);
        $paymentDataObject->method('getPayment')->willReturn(null);

        $buildSubject = ['payment' => $paymentDataObject];

        // Assert
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Order payment should be provided.');

        // Act
        $this->orderInfoBuilder->build($buildSubject);
    }
}
