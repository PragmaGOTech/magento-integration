<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Test\Unit\Service;

use Magento\Payment\Gateway\Command\CommandException;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\AcceptOrderPaymentInterface;
use Pragma\PragmaPayCore\Api\CancelOrderPaymentInterface;
use Pragma\PragmaPayWebApi\Service\NotifyOrderProcessor;
use Pragma\PragmaPayCore\Api\PragmaPaymentStatus;

class NotifyOrderProcessorTest extends TestCase
{
    private NotifyOrderProcessor $notifyOrderProcessor;
    private AcceptOrderPaymentInterface $acceptOrderPayment;
    private CancelOrderPaymentInterface $cancelOrderPayment;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->acceptOrderPayment = $this->createMock(AcceptOrderPaymentInterface::class);
        $this->cancelOrderPayment = $this->createMock(CancelOrderPaymentInterface::class);

        // Arrange: Instantiate the NotifyOrderProcessor
        $this->notifyOrderProcessor = new NotifyOrderProcessor(
            $this->acceptOrderPayment,
            $this->cancelOrderPayment
        );
    }

    public function testExecuteWithStatusFinanced(): void
    {
        // Arrange
        $status = PragmaPaymentStatus::STATUS_FINANCED;
        $pragmaPaymentId = '12345';
        $totalAmount = 20000; // in cents
        $orderIncrementId = '100000001';

        $this->acceptOrderPayment->expects($this->once())
            ->method('execute')
            ->with($pragmaPaymentId, 200.0, $orderIncrementId);

        // Act
        $this->notifyOrderProcessor->execute($status, $pragmaPaymentId, $totalAmount, $orderIncrementId);
    }

    public function testExecuteWithStatusCanceled(): void
    {
        // Arrange
        $status = PragmaPaymentStatus::STATUS_CANCELED;
        $pragmaPaymentId = '12345';
        $totalAmount = 20000; // in cents
        $orderIncrementId = '100000001';

        $this->cancelOrderPayment->expects($this->once())
            ->method('execute')
            ->with($pragmaPaymentId, $orderIncrementId);

        // Act
        $this->notifyOrderProcessor->execute($status, $pragmaPaymentId, $totalAmount, $orderIncrementId);
    }

    public function testExecuteWithUnknownStatus(): void
    {
        // Arrange
        $status = 'UNKNOWN';
        $pragmaPaymentId = '12345';
        $totalAmount = 20000; // in cents
        $orderIncrementId = '100000001';

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Unknown Action');

        // Act
        $this->notifyOrderProcessor->execute($status, $pragmaPaymentId, $totalAmount, $orderIncrementId);
    }
}
