<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Request;

use InvalidArgumentException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Client\RequestManager;
use Pragma\PragmaPayCore\Gateway\Request\RefundInfoBuilder;
use Pragma\PragmaPayCore\Service\CreditMemoIncrementIdGenerator;

class RefundInfoBuilderTest extends TestCase
{
    private RefundInfoBuilder $refundInfoBuilder;
    private RequestManager $requestManager;
    private CreditmemoInterface $creditMemo;
    private OrderPaymentInterface $orderPayment;
    private PaymentDataObjectInterface $paymentDataObject;
    private CreditMemoIncrementIdGenerator $creditMemoIncrementeIdGenerator;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->requestManager = $this->createMock(RequestManager::class);
        $this->creditMemoIncrementeIdGenerator = $this->createMock(CreditMemoIncrementIdGenerator::class);

        $this->creditMemo = $this->createMock(CreditmemoInterface::class);
        $this->orderPayment = $this->createMock(Payment::class);
        $this->paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);

        // Instantiate the RefundInfoBuilder with mocked dependencies
        $this->refundInfoBuilder = new RefundInfoBuilder(
            $this->requestManager,
            $this->creditMemoIncrementeIdGenerator
        );
    }

    public function testBuildWithValidData(): void
    {
        // Arrange
        $itemId = 'item123';
        $partnerUpdateId = 'creditmemo001';
        $grandTotal = 100.00;
        $formattedAmount = 10000;
        $currency = 'pl';

        $this->creditMemo->method('getIncrementId')->willReturn($partnerUpdateId);
        $this->creditMemo->method('getGrandTotal')->willReturn($grandTotal);
        $this->creditMemo->method('getOrderCurrencyCode')->willReturn($currency);

        $this->orderPayment->method('getAdditionalInformation')
            ->with(PragmaConnectionConfigProviderInterface::ADDITION_KEY_ITEM_ID)
            ->willReturn($itemId);

        $this->orderPayment->method('getCreditmemo')
            ->willReturn($this->creditMemo);

        $this->paymentDataObject->method('getPayment')->willReturn($this->orderPayment);

        $this->requestManager->method('formatAmount')->with($grandTotal)->willReturn($formattedAmount);

        $buildSubject = ['payment' => $this->paymentDataObject];

        // Act
        $result = $this->refundInfoBuilder->build($buildSubject);

        // Assert
        $this->assertArrayHasKey('body', $result);
        $this->assertSame([
            'itemId' => $itemId,
            'partnerUpdateId' => $partnerUpdateId,
            'value' => [
                'amount' => $formattedAmount,
                'currency' => $currency
            ],
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
        $this->refundInfoBuilder->build($buildSubject);
    }

    public function testBuildWithMissingCreditMemoThrowsException(): void
    {
        // Arrange
        $this->orderPayment->method('getCreditmemo')->willReturn(null);
        $this->paymentDataObject->method('getPayment')->willReturn($this->orderPayment);

        $buildSubject = ['payment' => $this->paymentDataObject];

        // Assert
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Credit memo is required for refund.');

        // Act
        $this->refundInfoBuilder->build($buildSubject);
    }
}
