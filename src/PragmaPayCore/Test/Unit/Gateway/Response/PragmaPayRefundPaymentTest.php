<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Gateway\Response\PragmaPayRefundPayment;

class PragmaPayRefundPaymentTest extends TestCase
{
    private CreditmemoRepositoryInterface $creditMemoRepository;
    private PragmaPayRefundPayment $pragmaPayRefundPayment;

    protected function setUp(): void
    {
        $this->creditMemoRepository = $this->createMock(CreditmemoRepositoryInterface::class);
        $this->pragmaPayRefundPayment = new PragmaPayRefundPayment($this->creditMemoRepository);
    }

    public function testHandleWithCreditmemo(): void
    {
        // Arrange
        $response = [
            'id' => '123456',
            'title' => 'Refund Title',
            'validTill' => '2025-05-01',
            'bankAccount' => 'PL12345678901234567890123456',
            'link' => 'https://example.com/refund-link',
        ];

        $creditMemo = $this->createMock(Creditmemo::class);
        $order = $this->createMock(Order::class);

        $payment = $this->createMock(Payment::class);
        $payment->method('getCreditmemo')->willReturn($creditMemo);

        $creditMemo->method('getOrder')->willReturn($order);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getPayment')->willReturn($payment);

        $handlingSubject = [
            'payment' => $paymentDataObject
        ];

        // Expectations
        $creditMemo->expects($this->once())->method('setTransactionId')->with($response['id']);
        $creditMemo->expects($this->exactly(3))->method('addComment');
        $order->expects($this->exactly(3))->method('addCommentToStatusHistory');

        $payment->expects($this->exactly(5))->method('setAdditionalInformation');
        $payment->expects($this->once())->method('setShouldCloseParentTransaction')->with(true);

        $this->creditMemoRepository->expects($this->once())->method('save')->with($creditMemo);

        // Act
        $this->pragmaPayRefundPayment->handle($handlingSubject, $response);

        // Assert (expectations verified by PHPUnit)
    }

    public function testHandleWithoutCreditmemo(): void
    {
        // Arrange
        $payment = $this->createMock(Payment::class);
        $payment->method('getCreditmemo')->willReturn(null);

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $paymentDataObject->method('getPayment')->willReturn($payment);

        $handlingSubject = [
            'payment' => $paymentDataObject
        ];

        $response = [];

        // Expectations
        $this->creditMemoRepository->expects($this->never())->method('save');

        // Act
        $this->pragmaPayRefundPayment->handle($handlingSubject, $response);

        // Assert (expectations verified by PHPUnit)
    }
}
