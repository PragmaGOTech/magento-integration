<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Service;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Pragma\PragmaPayWebApi\Api\ConfirmNotifyInterface;
use Pragma\PragmaPayWebApi\Api\Data\NotificationObjectInterface;
use Pragma\PragmaPayWebApi\Api\NotifyOrderProcessorInterface;
use Psr\Log\LoggerInterface;

class ConfirmNotify implements ConfirmNotifyInterface
{
    private NotifyOrderProcessorInterface $notifyOrderProcessor;

    private Json $json;

    private LoggerInterface $logger;

    public function __construct(NotifyOrderProcessorInterface $notifyOrderProcessor, Json $json, LoggerInterface $logger)
    {
        $this->notifyOrderProcessor = $notifyOrderProcessor;
        $this->json = $json;
        $this->logger = $logger;
    }

    public function execute(
        string $id,
        NotificationObjectInterface $object,
        string $type,
        string $date,
        string $timestamp
    ): string {
        try {
            $paymentId = $object->getPaymentId();
            $repaymentPeriod = $object->getRepaymentPeriod();
            $items = $object->getItems();

            $this->logger->notice('Received webhook PAYMENT_CHANGED', [
                'paymentId' => $paymentId,
                'repaymentPeriodType' => $repaymentPeriod->getType(),
                'repaymentPeriodValue' => $repaymentPeriod->getValue(),
                'itemsCount' => count($items)
            ]);

            foreach ($items as $item) {
                $this->logger->notice('Webhook Item', [
                    'partnerItemId' => $item->getPartnerItemId(),
                    'status' => $item->getStatus(),
                    'amount' => $item->getValue()->getAmount(),
                    'currency' => $item->getValue()->getCurrency()
                ]);

                $this->notifyOrderProcessor->execute(
                    $item->getStatus(),
                    $paymentId,
                    $item->getValue()->getAmount(),
                    $item->getPartnerItemId()
                );
            }
            return $this->json->serialize(['status' => 'success', 'message' => 'Webhook processed successfully.']);
        } catch (Exception $e) {
            $this->logger->error('Webhook processing error: ' . $e->getMessage());
            throw new LocalizedException(__('Webhook processing failed'));
        }
    }
}
