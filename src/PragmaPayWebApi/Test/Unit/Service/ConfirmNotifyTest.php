<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Test\Unit\Service;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayWebApi\Api\Data\NotificationItemInterface;
use Pragma\PragmaPayWebApi\Api\Data\NotificationItemValueInterface;
use Pragma\PragmaPayWebApi\Api\Data\NotificationObjectInterface;
use Pragma\PragmaPayWebApi\Api\Data\NotificationRepaymentPeriodInterface;
use Pragma\PragmaPayWebApi\Api\NotifyOrderProcessorInterface;
use Pragma\PragmaPayWebApi\Service\ConfirmNotify;
use Psr\Log\LoggerInterface;

class ConfirmNotifyTest extends TestCase
{
    private ConfirmNotify $confirmNotify;
    private NotifyOrderProcessorInterface $notifyOrderProcessor;
    private Json $json;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->notifyOrderProcessor = $this->createMock(NotifyOrderProcessorInterface::class);
        $this->json = $this->createMock(Json::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        // Arrange: Instantiate the ConfirmNotify class
        $this->confirmNotify = new ConfirmNotify(
            $this->notifyOrderProcessor,
            $this->json,
            $this->logger
        );
    }

    public function testExecuteProcessesWebhookSuccessfully(): void
    {
        // Arrange
        $notificationObject = $this->createMock(NotificationObjectInterface::class);
        $repaymentPeriod = $this->createMock(NotificationRepaymentPeriodInterface::class);
        $item = $this->createMock(NotificationItemInterface::class);
        $itemValue = $this->createMock(NotificationItemValueInterface::class);

        $notificationObject->method('getPaymentId')->willReturn('12345');
        $notificationObject->method('getRepaymentPeriod')->willReturn($repaymentPeriod);
        $notificationObject->method('getItems')->willReturn([$item]);

        $repaymentPeriod->method('getType')->willReturn('monthly');
        $repaymentPeriod->method('getValue')->willReturn('2023-10');

        $item->method('getPartnerItemId')->willReturn('item123');
        $item->method('getStatus')->willReturn('completed');
        $item->method('getValue')->willReturn($itemValue);

        $itemValue->method('getAmount')->willReturn(1000);
        $itemValue->method('getCurrency')->willReturn('USD');

        $this->json->method('serialize')->willReturn('{"status":"success","message":"Webhook processed successfully."}');

        // Act
        $result = $this->confirmNotify->execute('12345', $notificationObject, 'type', '2023-10-01', '1696156800');

        // Assert
        $this->assertSame('{"status":"success","message":"Webhook processed successfully."}', $result);
    }

    public function testExecuteHandlesException(): void
    {
        // Arrange
        $notificationObject = $this->createMock(NotificationObjectInterface::class);
        $notificationObject->method('getPaymentId')->willThrowException(new Exception('Error occurred'));

        $this->logger->expects($this->once())->method('error')->with('Webhook processing error: Error occurred');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Webhook processing failed');

        // Act
        $this->confirmNotify->execute('12345', $notificationObject, 'type', '2023-10-01', '1696156800');
    }
}
